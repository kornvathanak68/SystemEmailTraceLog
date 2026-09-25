<?php

namespace App\Jobs;

use App\Models\CompanyLookup;
use App\Models\ReportRun;
use App\Services\ReportParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParseDeliveryReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function __construct(public ReportRun $run)
    {
    }

    public function handle(ReportParser $parser): void
    {
        Log::info("ParseDeliveryReport: starting run #{$this->run->id}");
    
        try {
            $rows = $parser->parse($this->run->source_file);

            if (empty($rows)) {
                throw new \RuntimeException("Parser returned 0 rows from {$this->run->source_file}");
            }

            $this->run->deliveryRecords()->delete();

            $total = 0;
            $failed = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                $sentAt = $this->parseDate($row['sent_at'] ?? null);

                // Only keep rows that belong to the selected period (YYYY-MM)
                if ($sentAt && substr($sentAt, 0, 7) !== $this->run->period) {
                    $skipped++;
                    continue;
                }

                $subjectFilter = config('delivery.subject_filter');
                if ($subjectFilter && stripos($row['subject'] ?? '', $subjectFilter) === false) {
                    $skipped++;
                    continue;
                }

                $companyName = $row['company_name'] ?? null;
                $kamName     = $row['kam_name'] ?? null;

                if (empty($companyName) && !empty($row['recipient_email'])) {
                    $lookup = CompanyLookup::where('email', $row['recipient_email'])->first();
                    if ($lookup) {
                        $companyName = $lookup->company_name;
                        $kamName     = $lookup->kam_name;
                    }
                }

                $record = $this->run->deliveryRecords()->create([
                    'message_id'      => $row['message_id']      ?? null,
                    'recipient_email' => $row['recipient_email'] ?? null,
                    'company_name'    => $companyName,
                    'kam_name'        => $kamName,
                    'subject'         => $row['subject']         ?? null,
                    'sent_at'         => $sentAt,
                    'status'          => $row['status']          ?? null,
                    'failure_reason'  => $row['failure_reason']  ?? null,
                ]);

                $total++;

                if ($record->isFailed()) {
                    $failed++;
                }
            }

            $this->run->update([
                'total_rows'   => $total,
                'failed_count' => $failed,
                'stage'        => 'filtered',
                'status'       => 'running',
            ]);

            Log::info("ParseDeliveryReport: run #{$this->run->id} parsed", [
                'total'   => $total,
                'failed'  => $failed,
                'skipped' => $skipped,
            ]);

            MatchSystemLog::dispatch($this->run);

        } catch (\Throwable $e) {
            $this->run->update([
                'status' => 'failed',
                'error'  => $e->getMessage(),
            ]);

            Log::error("ParseDeliveryReport failed for run #{$this->run->id}", [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function parseDate(?string $value): ?string
    {
        if (!$value) return null;

        try {
            return \Carbon\Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}

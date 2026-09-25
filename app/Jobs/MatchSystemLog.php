<?php

namespace App\Jobs;

use App\Models\Pic;
use App\Models\ReportRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MatchSystemLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public ReportRun $run)
    {
    }

    public function handle(): void
    {
        Log::info("MatchSystemLog: run #{$this->run->id}");

        $pics = Pic::where('active', true)->get();

        if ($pics->isEmpty()) {
            Log::warning('MatchSystemLog: no active PICs configured. All failures will be unrouted.');
        }

        $failed = $this->run->deliveryRecords()
            ->whereIn('status', config('delivery.failure_statuses'))
            ->get();

        foreach ($failed as $record) {
            $pic = $this->matchPic($record, $pics);
            if ($pic) {
                $record->update(['pic_id' => $pic->id]);
            }
        }

        $this->run->update(['stage' => 'matched']);

        Log::info("MatchSystemLog: run #{$this->run->id} matched", [
            'failed' => $failed->count(),
        ]);

        PushToEcrm::dispatch($this->run);
    }

    protected function matchPic($record, $pics): ?Pic
    {
        foreach ($pics as $pic) {
            if (!$pic->match_rule) continue;

            // Rule syntax: "recipient_email contains @xyz.com" or "subject contains Invoice"
            // We keep it simple for now — substring match on either recipient or subject
            $rule = strtolower($pic->match_rule);

            if (str_contains(strtolower((string) $record->recipient_email), $rule)) {
                return $pic;
            }
            if (str_contains(strtolower((string) $record->subject), $rule)) {
                return $pic;
            }
        }

        // Fallback: first active PIC
        return $pics->first();
    }
}

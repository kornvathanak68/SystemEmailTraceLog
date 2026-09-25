<?php

namespace App\Jobs;

use App\Models\ReportRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPicReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public ReportRun $run)
    {
    }

    public function handle(): void
    {
        Log::info("SendPicReport: run #{$this->run->id}");

        $failed = $this->run->failedRecords()->get();

        if ($failed->isEmpty()) {
            Log::info("SendPicReport: no failures to report for run #{$this->run->id}");
        } else {
            $grouped = $failed->groupBy('pic_id');
            foreach ($grouped as $picId => $records) {
                Log::info("SendPicReport: would send " . $records->count() . " failures to PIC #{$picId}");
                // TODO: Mail::to($pic->email)->send(new DeliveryFailureReport($records));
            }
        }

        $this->run->update([
            'stage'  => 'reported',
            'status' => 'success',
        ]);

        Log::info("SendPicReport: run #{$this->run->id} complete");
    }
}

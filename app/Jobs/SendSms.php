<?php

namespace App\Jobs;

use App\Models\ReportRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public ReportRun $run)
    {
    }

    public function handle(): void
    {
        Log::info("SendSms: run #{$this->run->id}");

        if (!config('delivery.sms.enabled')) {
            Log::info('SendSms: SMS disabled in config, skipping');
            $this->run->update(['stage' => 'sms_sent']);
            SendPicReport::dispatch($this->run);
            return;
        }

        // TODO: call actual SMS gateway here (Twilio / Smart / Metfone / HTTP)
        // For now, just log and move on.

        Log::info('SendSms: (placeholder) would send SMS now');

        $this->run->update(['stage' => 'sms_sent']);

        SendPicReport::dispatch($this->run);
    }
}

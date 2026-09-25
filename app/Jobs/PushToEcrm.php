<?php

namespace App\Jobs;

use App\Models\ReportRun;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushToEcrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public ReportRun $run)
    {
    }

    public function handle(): void
    {
        Log::info("PushToEcrm: run #{$this->run->id} (mode: " . config('delivery.ecrm.mode') . ")");

        if (config('delivery.dry_run')) {
            Log::warning('PushToEcrm: DRY RUN — skipping actual push');
            $this->run->update(['stage' => 'pushed']);
            SendSms::dispatch($this->run);
            return;
        }

        // TODO: implement depending on eCRM integration mode (api|database|file|ui)
        // For now, this is a placeholder that logs and moves on.

        $this->run->update(['stage' => 'pushed']);

        SendSms::dispatch($this->run);
    }
}

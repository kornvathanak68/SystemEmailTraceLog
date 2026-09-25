<?php

namespace App\Console\Commands;

use App\Models\ReportRun;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FetchDeliveryReport extends Command
{
    protected $signature = 'report:fetch
                            {--period= : Force a period like 2026-09}
                            {--file= : Force a specific file path}
                            {--dry-run : Do everything except dispatch the job}';

    protected $description = 'Fetch the EA delivery report file and start the pipeline';

    public function handle(): int
    {
        $period = $this->option('period') ?: now()->format('Y-m');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Period: {$period}");
        if ($dryRun) {
            $this->warn('DRY RUN — nothing will be dispatched.');
        }

        // 1. Idempotency check
        $existing = ReportRun::where('period', $period)->first();
        if ($existing && $existing->status === 'success') {
            $this->error("Run for {$period} already completed successfully. Skipping.");
            return self::FAILURE;
        }

        // 2. Find the file
        $file = $this->option('file') ?: $this->findIncomingFile($period);

        if (!$file || !File::exists($file)) {
            $this->error("No incoming file found for period {$period} in " . config('delivery.incoming_path'));
            Log::error('report:fetch — no file found', ['period' => $period]);
            return self::FAILURE;
        }

        $this->info("Found file: {$file}");

        // 3. Create or update the run
        $run = ReportRun::updateOrCreate(
            ['period' => $period],
            [
                'source_file' => $file,
                'stage'       => 'fetched',
                'status'      => 'running',
                'error'       => null,
            ]
        );

        // 4. Move file to processed (keep the raw untouched copy)
        $processedDir = config('delivery.processed_path');
        File::ensureDirectoryExists($processedDir);
        $processedPath = $processedDir . DIRECTORY_SEPARATOR . basename($file);
        File::copy($file, $processedPath);
        $run->update(['filtered_file' => $processedPath]);

        $this->info("Run #{$run->id} created (stage: fetched).");

        if ($dryRun) {
            $this->warn('Dry run — stopping here. Would dispatch ParseDeliveryReport next.');
            return self::SUCCESS;
        }

        // 5. Dispatch the next job in the chain
        \App\Jobs\ParseDeliveryReport::dispatch($run);

        $this->info('Dispatched ParseDeliveryReport job.');
        return self::SUCCESS;
    }

    /**
     * Find the newest matching file in the incoming folder for this period.
     */
    protected function findIncomingFile(string $period): ?string
    {
        $dir = config('delivery.incoming_path');
        if (!File::isDirectory($dir)) {
            return null;
        }

        $exts = config('delivery.accepted_ext', ['xlsx', 'xls', 'csv']);

        $candidates = collect(File::files($dir))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), $exts))
            ->sortByDesc(fn ($f) => $f->getMTime())
            ->values();

        if ($candidates->isEmpty()) {
            return null;
        }

        return $candidates->first()->getPathname();
    }
}

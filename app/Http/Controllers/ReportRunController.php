<?php

namespace App\Http\Controllers;

use App\Jobs\ParseDeliveryReport;
use App\Models\ReportRun;
use App\Services\DeliveryExcelExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ReportRunController extends Controller
{
    public function uploadForm()
    {
        return Inertia::render('Runs/Upload');
    }

    public function upload(Request $request, DeliveryExcelExporter $exporter)
    {
        $request->validate([
            'period' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'file'   => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        $period = $request->input('period');

        $incomingDir = config('delivery.incoming_path');
        File::ensureDirectoryExists($incomingDir);

        $original = $request->file('file')->getClientOriginalName();
        $safe     = preg_replace('/[^A-Za-z0-9._-]/', '_', $original);
        $filename = $period . '-' . now()->format('Ymd-His') . '-' . $safe;

        $request->file('file')->move($incomingDir, $filename);
        $path = $incomingDir . DIRECTORY_SEPARATOR . $filename;

        $run = ReportRun::updateOrCreate(
            ['period' => $period],
            [
                'source_file' => $path,
                'stage'       => 'fetched',
                'status'      => 'running',
                'error'       => null,
            ]
        );

        Log::info("Upload: saved file for period {$period}", ['path' => $path]);

        ParseDeliveryReport::dispatch($run);

        $run->refresh();

        $excelPath = $exporter->export($run);

        return response()->download($excelPath)->deleteFileAfterSend(false);
    }
}

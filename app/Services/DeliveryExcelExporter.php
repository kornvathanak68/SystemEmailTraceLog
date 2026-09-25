<?php

namespace App\Services;

use App\Models\ReportRun;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;
use Illuminate\Support\Facades\File;

class DeliveryExcelExporter
{
    public function export(ReportRun $run): string
    {
        $outputDir = config('delivery.output_path');
        File::ensureDirectoryExists($outputDir);

        $filename = "TraceResult-{$run->period}.xlsx";
        $path = $outputDir . DIRECTORY_SEPARATOR . $filename;

        $writer = new Writer();
        $writer->openToFile($path);

        $writer->addRow(Row::fromValues([
            'DATE TIME',
            'EVENT ID',
            'EMAIL SUBJECT',
            'RECICENT STATUS',
            'SEND_ADDR',
            'COMPANY NAME',
        ]));

        $failedRecords = $run->failedRecords()->get();

        foreach ($failedRecords as $record) {
            $writer->addRow(Row::fromValues([
                $record->sent_at?->format('Y-m-d H:i:s') ?? '',
                $record->status ?? '',
                $record->subject ?? '',
                $record->failure_reason ?? '',
                $record->recipient_email ?? '',
                $record->company_name ?? '',
            ]));
        }

        $writer->close();

        return $path;
    }
}

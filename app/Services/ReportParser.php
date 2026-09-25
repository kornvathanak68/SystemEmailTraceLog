<?php

namespace App\Services;

use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use Illuminate\Support\Facades\Log;

class ReportParser
{
    public function parse(string $filePath): array
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $reader = match ($ext) {
            'xlsx', 'xls' => new XlsxReader(),
            'csv'         => new CsvReader(),
            default       => throw new \RuntimeException("Unsupported file type: {$ext}"),
        };

        $reader->open($filePath);
        $headerMap = [];
        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $i => $row) {
                $cells = $row->toArray();

                if ($i === 1) {
                    $headerMap = $this->mapHeaders($cells);
                    continue;
                }

                if ($this->isEmptyRow($cells)) {
                    continue;
                }

                $parsed = $this->rowToAssoc($cells, $headerMap);
                if (!empty($parsed)) {
                    $rows[] = $parsed;
                }
            }
            break;
        }

        $reader->close();

        Log::info("ReportParser: parsed {$filePath}", ['rows' => count($rows)]);

        return $rows;
    }

    protected function mapHeaders(array $headerCells): array
    {
        $aliases = config('delivery.columns', []);
        $map = [];

        foreach ($headerCells as $index => $rawHeader) {
            $normalized = strtolower(trim((string) $rawHeader));
            if ($normalized === '') continue;

            foreach ($aliases as $ourField => $knownNames) {
                foreach ($knownNames as $known) {
                    if ($normalized === strtolower($known)) {
                        $map[$index] = $ourField;
                        continue 3;
                    }
                }
            }

            $map[$index] = $normalized;
        }

        return $map;
    }

    protected function rowToAssoc(array $cells, array $headerMap): array
    {
        $out = [];
        foreach ($headerMap as $index => $field) {
            $out[$field] = isset($cells[$index]) ? trim((string) $cells[$index]) : null;
        }
        return $out;
    }

    protected function isEmptyRow(array $cells): bool
    {
        foreach ($cells as $c) {
            if (trim((string) $c) !== '') return false;
        }
        return true;
    }
}

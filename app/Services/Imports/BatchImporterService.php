<?php

namespace App\Services\Imports;

use App\Models\DataImportLog;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Imports\ChunkedDynamicDataImport;

class BatchImporterService
{
    /**
     * Call custom import object with chunks
     *
     * @param string $filePath
     * @param array $config
     * @param DataImportLog $importLog
     * @return void
     */
    public static function runInChunks(string $filePath, array $config, DataImportLog $importLog): void
    {
        Excel::import(
            new ChunkedDynamicDataImport($config, $importLog, $filePath),
            $filePath
        );
    }
}

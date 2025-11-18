<?php

namespace App\Events;

use App\Models\DataImportLog;
use App\Services\Imports\ImportLogger;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportCompleted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var DataImportLog
     */
    public DataImportLog $log;

    /**
     * @var string
     */
    public string $filePath;

    /**
     * @var ImportLogger
     */
    public ImportLogger $importLogger;


    /**
     * Create a new event instance.
     */
    public function __construct(DataImportLog $log, string $filePath)
    {
        $this->log = $log;
        $this->filePath = $filePath;
        $this->importLogger = new ImportLogger($log);
    }
}

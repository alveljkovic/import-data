<?php

namespace App\Events;

use App\Models\DataImportLog;
use App\Services\Imports\ImportLogger;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportFailed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var ImportLog
     */
    public $log;

    /**
     * @var string
     */
    public $filePath;

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
        $this->importLogger = new ImportLogger($log);
        $this->filePath = $filePath;
    }
}

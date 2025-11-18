<?php

namespace App\Services\Imports;

use App\Models\DataImportLog;
use App\Models\DataImportAudit;
use App\Models\DataImportError;

class ImportLogger
{
    /**
     * DataImportLog model
     *
     * @var DataImportLog
     */
    protected DataImportLog $log;

    public function __construct(DataImportLog $log)
    {
        $this->log = $log;
    }

    /**
     * Create a new import log entry
     *
     * @param array $logData
     * @return DataImportLog
     */
    public static function createLog(array $logData): DataImportLog
    {
        $log = DataImportLog::create($logData);
        return $log;
    }

    /**
     * Add DataImportError batch data
     *
     * @param array $errors
     * @return void
     */
    public function logRowError(array $errors): void
    {
        DataImportError::insert($errors);
    }

    /**
     * Add DataImportAudit batch data
     *
     * @param array $changes
     * @return void
     */
    public function logAuditChanges(array $changes): void
    {
        DataImportAudit::insert($changes);
    }

    /**
     * Update the import log entry
     *
     * @param array $data
     * @return void
     */
    public function updateLog(array $data): void
    {
        $this->log->update($data);
    }
}

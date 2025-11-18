<?php

namespace App\Jobs;

use App\Events\ImportCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\ImportFailed;
use App\Models\DataImportLog;
use Illuminate\Support\Facades\Storage;
use App\Services\Imports\BatchImporterService;
use App\Services\Imports\ImportLogger;

class ProcessDataImport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected DataImportLog $importLog;
    protected string $filePath;
    protected array $fileConfig;
    protected ImportLogger $importLogger;

    /**
     * Create a new job instance.
     */
    public function __construct(DataImportLog $importLog, string $filePath, array $fileConfig)
    {
        $this->importLog = $importLog;
        $this->filePath = $filePath;
        $this->fileConfig = $fileConfig;
        $this->importLogger = new ImportLogger($importLog);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            BatchImporterService::runInChunks($this->filePath, $this->fileConfig, $this->importLog);
        } catch (\Exception $e) {
            event(new ImportFailed($this->importLog, $this->filePath));
        }
    }
}

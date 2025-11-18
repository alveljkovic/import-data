<?php

namespace App\Services\Imports;

use App\Interfaces\DataImportInterface;
use App\Http\Requests\DataImportRequest;
use App\Jobs\ProcessDataImport;
use App\Models\DataImportLog;
use App\Traits\DataImportConfigurationTrait;
use App\Services\Imports\ImportLogger;
use App\Validations\FileHeaderValidator;

class BackgroundDataImporter implements DataImportInterface
{
    use DataImportConfigurationTrait;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Method for dispatching import jobs by file
     *
     * @param DataImportRequest $request
     * @return array
     */
    public function process(DataImportRequest $request): array
    {
        $typeKey = $request->input('import_type_key');
        $files = $request->allFiles();

        foreach ($files as $key => $file) {
            $fileKey = $this->getFileKeyFromInput($key);
            $fileConfig = $this->getFileConfig($typeKey, $fileKey);
            $path = $file->storeAs('imports', time() . '-' . $file->getClientOriginalName());

            FileHeaderValidator::validate($path, $fileConfig);

            $importLog = ImportLogger::createLog([
                'user_id' => $this->user->id,
                'import_type_key' => $typeKey,
                'file_config_key' => $fileKey,
                'original_filename' => $file->getClientOriginalName(),
                'started_at' => now(),
                'status' => 'pending',
            ]);

            ProcessDataImport::dispatch($importLog, $path, $fileConfig);
        }

        return ['status' => 'success', 'message' => 'Import has been added to the queue.', 'data' => []];
    }
}

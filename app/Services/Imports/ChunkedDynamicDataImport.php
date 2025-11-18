<?php

namespace App\Services\Imports;

use App\Events\ImportCompleted;
use App\Events\ImportFailed;
use App\Models\DataImportLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Validator;
use App\Services\Imports\ImportLogger;

class ChunkedDynamicDataImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected array $config;
    protected DataImportLog $importLog;
    protected ImportLogger $importLogger;
    protected string $filePath;

    public function __construct(array $config, DataImportLog $importLog, string $filePath)
    {
        $this->config = $config;
        $this->importLog = $importLog;
        $this->importLogger = new ImportLogger($this->importLog);
        $this->filePath = $filePath;
    }

    /**
     * Main method called on chunk
     *
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows): void
    {
        $table = $this->config['table_name'];
        $headersMap = $this->config['headers_to_db'];
        $updateKeys = $this->config['update_or_create'] ?? [];


        $validRows = [];
        $rowNumberMap = [];
        $errors = [];

        foreach ($rows->toArray() as $i => $row) {
            $rowNumber = $i + 2;

            $dbData = $this->mapRow($row, $headersMap);

            $validationErrors = $this->validateRow($dbData, $headersMap);
            if (!empty($validationErrors)) {
                foreach ($validationErrors as $col => $messages) {
                    $errors[] = [
                        'data_import_log_id' => $this->importLog->id,
                        'row_number' => $rowNumber,
                        'column_name' => $col,
                        'value' => $dbData[$col],
                        'message' => implode(', ', $messages),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                continue;
            }

            $validRows[] = $dbData;
            // mapping row number for update/create later
            $rowNumberMap[implode('|', array_map(fn($k) => $dbData[$k], $updateKeys))] = $rowNumber;
        }

        // Insert errors in batch
        if (!empty($errors)) {
            $this->importLogger->logRowError($errors);
        }

        // Insert or Update/Create valid rows
        if (!empty($validRows)) {
            if (empty($updateKeys)) {
                DB::table($table)->insert($validRows);
            } else {
                $this->bulkUpsertWithAudit($table, $validRows, $updateKeys, $rowNumberMap);
            }
        }

        // Dispatch Success/Failed event
        (empty($errors)) ?
            event(new ImportCompleted($this->importLog, $this->filePath)) :
            event(new ImportFailed($this->importLog, $this->filePath));
    }

    /**
     * Bulk upsert with audit logging
     *
     * @param string $table
     * @param array $rows
     * @param array $updateKeys
     * @param array $rowNumberMap
     * @return void
     */
    protected function bulkUpsertWithAudit(string $table, array $rows, array $updateKeys, array $rowNumberMap)
    {
        // Fetch existing rows
        $existingRows = DB::table($table)
            ->where(function ($q) use ($rows, $updateKeys) {
                foreach ($rows as $r) {
                    $q->orWhere(function ($sub) use ($updateKeys, $r) {
                        foreach ($updateKeys as $k) {
                            $sub->where($k, $r[$k]);
                        }
                    });
                }
            })
            ->get()
            ->keyBy(fn($item) => implode('|', collect($updateKeys)->map(fn($k) => $item->$k)->toArray()));

        // Prepare audit for rows that exist **and have changes**
        $audits = [];
        foreach ($rows as $r) {
            $key = implode('|', array_map(fn($k) => $r[$k], $updateKeys));
            if (isset($existingRows[$key])) {
                $existing = $existingRows[$key];
                $rowNumber = $rowNumberMap[$key] ?? null;
                foreach ($r as $col => $val) {
                    $oldVal = $existing->$col ?? null;
                    if ($oldVal != $val) {
                        $audits[] = [
                            'data_import_log_id' => $this->importLog->id,
                            'table_name' => $table,
                            'row_id' => $existing->id,
                            'row_number' => $rowNumber,
                            'column_name' => $col,
                            'old_value' => $oldVal,
                            'new_value' => $val,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }
        }

        if (!empty($audits)) {
            $this->importLogger->logAuditChanges($audits);
        }

        // Upsert only rows that **are new or changed**
        $rowsToUpsert = [];
        foreach ($rows as $r) {
            $key = implode('|', array_map(fn($k) => $r[$k], $updateKeys));
            if (!isset($existingRows[$key]) || !empty(array_filter($r, fn($val, $col) => $existingRows[$key]->$col != $val, ARRAY_FILTER_USE_BOTH))) {
                $rowsToUpsert[] = $r;
            }
        }

        if (!empty($rowsToUpsert)) {
            DB::table($table)->upsert($rowsToUpsert, $updateKeys, array_keys($rows[0]));
        }
    }


    /**
     * Mapping file row in DB columns
     *
     * @param array $row
     * @param array $headersMap
     * @return array
     */
    protected function mapRow(array $row, array $headersMap): array
    {
        $dbData = [];
        foreach ($headersMap as $excelHeader => $map) {
            $safeHeader = strtolower(str_replace([' ', '#'], ['_', ''], $excelHeader));
            $val = $row[$safeHeader] ?? null;

            if ($map['type'] === 'date' && is_numeric($val)) {
                $val = Date::excelToDateTimeObject($val)->format('Y-m-d');
            }

            $dbData[$map['db_column']] = $this->formatValue($val, $map['type']);
        }

        return $dbData;
    }

    /**
     * Validation per row
     *
     * @param array $dbData
     * @param array $headersMap
     * @return array
     */
    protected function validateRow(array $dbData, array $headersMap): array
    {
        $rules = [];
        $attributes = [];
        foreach ($headersMap as $excelHeader => $map) {
            $rules[$map['db_column']] = $map['validation'] ?? [];
            $attributes[$map['db_column']] = $excelHeader;
        }

        $validator = Validator::make($dbData, $rules, [], $attributes);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }

    /**
     * Formatting value to appropriate type
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function formatValue($value, string $type)
    {
        return match ($type) {
            'date' => $value ? \Carbon\Carbon::parse($value)->toDateString() : null,
            'double' => $value !== null ? (double) str_replace(',', '.', $value) : null,
            'integer' => $value !== null ? (int) $value : null,
            default => $value,
        };
    }

    /**
     * Overriding chunk size - custom chunk size
     *
     * @return integer
     */
    public function chunkSize(): int
    {
        return 500;
    }
}

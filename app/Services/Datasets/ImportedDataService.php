<?php

namespace App\Services\Datasets;

use App\Models\DataImportAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ImportedDataService
{
    /**
     * IMport Type configuration
     *
     * @var array
     */
    protected array $importType;

    /**
     * File configuration
     *
     * @var array
     */
    protected array $fileKey;

    /**
     * File table name
     *
     * @var string
     */
    protected string $tableName;

    /**
     * File header config
     *
     * @var array
     */
    protected array $headersConfig;

    /**
     * File db columns
     *
     * @var array
     */
    protected array $dbColumns;

    /**
     * Import Type persmission
     *
     * @var string
     */
    protected string $permissionRequired;

    /**
     * Can user delete rows
     *
     * @var boolean
     */
    protected bool $canDelete;

    /**
     * Logged user
     *
     * @var User
     */
    protected User $user;

    public function __construct(array $importType, array $fileKey, User $user)
    {
        $this->importType = $importType;
        $this->fileKey = $fileKey;
        $this->loadConfiguration();
    }

    /**
     * Loads configuration for view
     *
     * @return void
     */
    protected function loadConfiguration(): void
    {
        $this->tableName = $this->fileKey['table_name'];
        $this->headersConfig = $this->fileKey['headers_to_db'];
        $this->dbColumns = Arr::pluck($this->headersConfig, 'db_column');
        $this->dbColumns[] = 'id';
        $this->permissionRequired = $this->importType['permission_required'] ?? null;
        $this->canDelete = $this->canUserDelete();
    }

    // --- GETTER METODE ---
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getPermissionRequired(): ?string
    {
        return $this->permissionRequired;
    }

    public function getDisplayHeaders(): array
    {
        return array_keys($this->headersConfig);
    }

    public function getHeadersConfig(): array
    {
        return $this->headersConfig;
    }

    public function getCanDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * Check user permission for delete
     *
     * @return boolean
     */
    private function canUserDelete(): bool
    {
        if ($this->permissionRequired) {
            return auth()->user()->can($this->permissionRequired);
        }

        return false;
    }

    /**
     * Fetches paginated data with optional search across all columns.
     * @param Request $request
     * @return LengthAwarePaginator
     * @throws \RuntimeException
     */
    public function getPaginatedData(Request $request): LengthAwarePaginator
    {
        $query = DB::table($this->tableName)->select($this->dbColumns);
        $search = $request->input('search');

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                foreach ($this->dbColumns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        return $query->paginate(20)->withQueryString();
    }

    /**
     * Deletes a row from the table based on ID.
     *
     * @param int $rowId
     * @return int Number of deleted rows.
     * @throws \RuntimeException
     */
    public function deleteRowById(int $rowId): int
    {
        return DB::table($this->tableName)->where('id', $rowId)->delete();
    }

    /**
     * Fetches the actual audit history for a given row.
     * Assumes there is a table 'imported_data_audits' with columns:
     * table_name, row_id, column_name, old_value, new_value, user_id, created_at.
     *
     * @param int $rowId
     * @return array
     */
    public function getAuditsForRow(int $rowId): array
    {
        try {
            $audits = DataImportAudit::where('row_id', $rowId)->get();
            return $audits->map(function ($audit) {
                return [
                    'table' => $audit->table_name,
                    'number' => $audit->row_number,
                    'column' => $audit->column_name,
                    'old' => $audit->old_value ?? 'N/A',
                    'new' => $audit->new_value ?? 'N/A',
                    'timestamp' => $audit->created_at,
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error("Error fetching audit: " . $e->getMessage(), [
                'table' => $this->tableName,
                'row_id' => $rowId
            ]);
            return [];
        }
    }
}

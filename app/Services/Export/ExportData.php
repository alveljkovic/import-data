<?php

namespace App\Services\Export;

use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportData implements FromCollection, WithHeadings
{
    protected array $fileKey;
    protected ?string $search;

    public function __construct(array $fileKey, ?string $search = null)
    {
        $this->fileKey = $fileKey;
        $this->search = $search;
    }

    /**
     * Fetching data for export
     *
     * @return void
     */
    public function collection()
    {
        $columns = Arr::pluck($this->fileKey['headers_to_db'], 'db_column');
        $query = DB::table($this->fileKey['table_name'])->select($columns);

        if ($this->search) {
            $query->where(function ($q) use ($columns) {
                foreach ($columns as $index => $column) {
                    if ($index === 0) {
                        $q->where($column, 'like', "%{$this->search}%");
                    } else {
                        $q->orWhere($column, 'like', "%{$this->search}%");
                    }
                }
            });
        }

        return $query->get();
    }

    /**
     * Setting file header
     *
     * @return array
     */
    public function headings(): array
    {
        return array_keys($this->fileKey['headers_to_db']);
    }
}

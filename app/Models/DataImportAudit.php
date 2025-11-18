<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataImportAudit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'import_log_id',
        'table_name',
        'row_id',
        'row_number',
        'column_name',
        'old_value',
        'new_value',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'old_value' => 'string',
        'new_value' => 'string',
    ];

    /**
     * DataImportLog model relation
     *
     * @return BelongsTo
     */
    public function importLog(): BelongsTo
    {
        return $this->belongsTo(DataImportLog::class);
    }
}

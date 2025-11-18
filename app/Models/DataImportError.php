<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataImportError extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'data_import_log_id',
        'row_number',
        'column_name',
        'value',
        'message',
    ];

    /**
     * Relation to DataImportLog model
     */
    public function importLog(): BelongsTo
    {
        return $this->belongsTo(DataImportLog::class);
    }
}

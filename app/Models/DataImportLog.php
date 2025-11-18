<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataImportLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'import_type_key',
        'file_config_key',
        'original_filename',
        'status',
        'started_at',
        'completed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * User model relation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * DataImportAudit model relation
     *
     * @return HasMany
     */
    public function audits(): HasMany
    {
        return $this->hasMany(DataImportAudit::class);
    }

    /**
     * Realtion with DataIMportError model
     *
     * @return HasMany
     */
    public function errors(): HasMany
    {
        return $this->hasMany(DataImportError::class);
    }
}

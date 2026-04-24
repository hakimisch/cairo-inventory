<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'description',
        'contract_no',
        'company_name',
        'cost',
        'status',
        'notes',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost'             => 'decimal:2',
    ];

    /**
     * Get the asset that owns this maintenance record.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}

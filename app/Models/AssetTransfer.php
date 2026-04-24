<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetTransfer extends Model
{
    protected $fillable = [
        'asset_id',
        'from_location',
        'to_location',
        'from_custodian',
        'to_custodian',
        'transfer_date',
        'reference_no',
        'status',       // pending, approved, completed, cancelled
        'reason',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    /**
     * Get the asset being transferred.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}

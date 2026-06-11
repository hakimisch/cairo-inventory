<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetTransfer extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DisposalSaleItem extends Model
{
    use LogsActivity;
    protected $fillable = [
        'disposal_sale_id',
        'asset_id',
        'item_description',
        'quantity',
        'reserve_price',
        'estimated_value',
        'lot_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity'        => 'integer',
        'reserve_price'   => 'decimal:2',
        'estimated_value' => 'decimal:2',
    ];

    /**
     * Get the parent sale.
     */
    public function disposalSale(): BelongsTo
    {
        return $this->belongsTo(DisposalSale::class);
    }

    /**
     * Get the asset being sold (optional).
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get all bids for this item.
     */
    public function saleBids(): HasMany
    {
        return $this->hasMany(SaleBid::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

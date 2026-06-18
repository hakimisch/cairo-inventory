<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class PurchaseOrderItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'purchase_order_id',
        'item_code',
        'description',
        'brand',
        'model',
        'category',
        'quantity_ordered',
        'unit',
        'unit_price',
        'total_price',
        'status',
        'notes',
        'do_line_item_id',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'unit_price'       => 'decimal:2',
        'total_price'      => 'decimal:2',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function doLineItem(): BelongsTo
    {
        return $this->belongsTo(DoLineItem::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    // ─── Activity Log ──────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

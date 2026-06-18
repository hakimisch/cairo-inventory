<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DoLineItem extends Model
{
    use LogsActivity;

    protected $fillable = [
        'delivery_order_id',
        'category',
        'item_code',
        'description',
        'brand',
        'model',
        'serial_number',
        'quantity_ordered',
        'quantity_received',
        'unit',
        'status',
        'scan_user_id',
        'scanned_at',
        'notes',
        'has_serial',
        'serial_editable',
    ];

    protected $casts = [
        'quantity_ordered'  => 'integer',
        'quantity_received' => 'integer',
        'scanned_at'        => 'datetime',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scan_user_id');
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class SaleBid extends Model
{
    use LogsActivity;
    protected $fillable = [
        'disposal_sale_item_id',
        'bidder_name',
        'bidder_ic',
        'bidder_phone',
        'bidder_address',
        'bid_amount',
        'bid_date',
        'deposit_paid',
        'deposit_amount',
        'is_winner',
        'award_date',
        'payment_date',
        'payment_received',
        'status',
        'notes',
    ];

    protected $casts = [
        'bid_date'         => 'datetime',
        'award_date'       => 'date',
        'payment_date'     => 'date',
        'bid_amount'       => 'decimal:2',
        'deposit_amount'   => 'decimal:2',
        'deposit_paid'     => 'boolean',
        'is_winner'        => 'boolean',
        'payment_received' => 'boolean',
    ];

    /**
     * Get the sale item this bid is for.
     */
    public function disposalSaleItem(): BelongsTo
    {
        return $this->belongsTo(DisposalSaleItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DisposalSale extends Model
{
    use LogsActivity;
    protected $fillable = [
        'asset_disposal_id',
        'sale_type',
        'sale_reference',
        'sale_date',
        'sale_location',
        'viewing_date_start',
        'viewing_date_end',
        'closing_datetime',
        'sealed_envelope_ref',
        'tender_box_address',
        'bid_validity_days',
        'sale_officer',
        'description',
        'terms_conditions',
        'deposit_required',
        'sale_status',
        'decision_date',
        'decision_notes',
        'report_date',
        'report_notes',
        'certificate_date',
        'certificate_type',
        'certificate_reference',
        'status',
        'notes',
        'signatures',
    ];

    protected $casts = [
        'sale_date'        => 'date',
        'viewing_date_start' => 'date',
        'viewing_date_end'   => 'date',
        'closing_datetime'   => 'datetime',
        'decision_date'    => 'date',
        'report_date'      => 'date',
        'certificate_date' => 'date',
        'deposit_required' => 'decimal:2',
        'signatures'       => 'array',
    ];

    /**
     * Get the parent disposal for this sale.
     */
    public function assetDisposal(): BelongsTo
    {
        return $this->belongsTo(AssetDisposal::class);
    }

    /**
     * Get all items in this sale.
     */
    public function disposalSaleItems(): HasMany
    {
        return $this->hasMany(DisposalSaleItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

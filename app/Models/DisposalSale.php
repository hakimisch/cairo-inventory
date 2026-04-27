<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisposalSale extends Model
{
    protected $fillable = [
        'asset_disposal_id',
        'sale_type',
        'sale_reference',
        'sale_date',
        'sale_location',
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
    ];

    protected $casts = [
        'sale_date'        => 'date',
        'decision_date'    => 'date',
        'report_date'      => 'date',
        'certificate_date' => 'date',
        'deposit_required' => 'decimal:2',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MasterItem extends Model
{
    use SoftDeletes;
    /**
     * The canonical equipment register — all 156 items from the master Excel.
     *
     * This is THE authoritative list of every item across all 5 labs.
     * Items flow: Import → Master → Verify → PA-1 → PA-2 or PA-3.
     */
    protected $fillable = [
        'item_no',
        'category',
        'name',
        'specification',
        'brand',
        'serial_numbers',
        'country_of_origin',
        'unit_price',
        'total_price',
        'sodo_bayaran',
        'supplier_id',
        'quantity',
        'status',           // belum_diterima | diterima | verified
        'received_date',
        'do_no',
        'invoice_no',
        'verified_at',
        'verified_by',
        'asset_type',       // fixed_asset | inventory
        'receive_meta',     // JSON: receiver metadata stored at receive time, used at verify
    ];

    protected $casts = [
        'quantity'      => 'integer',
        'unit_price'    => 'decimal:2',
        'total_price'   => 'decimal:2',
        'received_date' => 'date:Y-m-d',
        'verified_at'   => 'datetime',
        'receive_meta'  => 'array',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * The PA-1 receiving record created when this item was verified.
     */
    public function receiving(): HasOne
    {
        return $this->hasOne(Receiving::class, 'master_item_id');
    }

    /**
     * The PA-2 or PA-3 asset record created from this item.
     */
    public function asset(): HasOne
    {
        return $this->hasOne(Asset::class, 'master_item_id');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDiterima($query)
    {
        return $query->where('status', 'diterima');
    }

    public function scopeBelumDiterima($query)
    {
        return $query->where('status', 'belum_diterima');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeNotVerified($query)
    {
        return $query->whereIn('status', ['belum_diterima', 'diterima']);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────

    /**
     * Parse the comma-separated serial_numbers field into a clean array.
     * Returns empty array when no serial numbers exist.
     */
    public function getSerialNumbersArray(): array
    {
        if (empty($this->serial_numbers)) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $this->serial_numbers)),
            fn($s) => $s !== ''
        ));
    }
}

<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Asset extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'asset_tag',
        'name',
        'category',
        'asset_type',         // 'fixed_asset' | 'inventory'
        'campus',             // 'utm_kl' | 'utm_jb' | 'other'
        'purchase_price',
        'location',
        'status',
        'national_code',
        'supplier_name',
        'supplier_address',
        'po_reference',
        'do_reference',
        'warranty_period',
        'warranty_expiry',    // date — warranty tracker
        'received_date',
        'rejection_reason',
        'receiver_name',
        'custodian_name',
        'image_url',
        'components',
        'model',
        'brand',
        'serial_number',
        'requires_maintenance',
        'saga_id',
        'voucher_no',
        'budget_vot',
    ];
 
    protected $casts = [
        'components'      => 'array',
        'warranty_expiry' => 'date',
        'received_date'   => 'date',
        'requires_maintenance' => 'boolean',
    ];
 
    // ─── Scopes ───────────────────────────────────────────────────────────────
    public function scopeFixedAssets($query)
    {
        return $query->where('asset_type', 'fixed_asset');
    }
 
    public function scopeInventory($query)
    {
        return $query->where('asset_type', 'inventory');
    }
 
    public function scopeByCampus($query, string $campus)
    {
        return $query->where('campus', $campus);
    }

    public function placements()
    {
        // Orders by newest first so we see the current location at the top
        return $this->hasMany(AssetPlacement::class)->orderBy('assigned_date', 'desc');
    }
}
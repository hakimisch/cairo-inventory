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
        'image_url',
        'components',
    ];
 
    protected $casts = [
        'components'      => 'array',
        'warranty_expiry' => 'date',
        'received_date'   => 'date',
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
}
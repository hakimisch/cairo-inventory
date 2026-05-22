<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Asset extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity;

    protected $fillable = [
        'asset_tag',
        'name',
        'category',
        'sub_category',          // NEW — Sub Jenis (KEW.PA-2/PA-3)
        'asset_type',            // 'fixed_asset' | 'inventory'
        'campus',                // 'utm_kl' | 'utm_jb' | 'other'
        'purchase_price',
        'location',
        'status',
        'quantity',              // NEW — for inventory items (KEW.PA-3)
        'unit_of_measure',       // NEW — Unit Pengukuran (KEW.PA-3)
        'national_code',
        'supplier_name',
        'supplier_address',
        'po_reference',
        'do_reference',
        'warranty_period',
        'warranty_expiry',       // date — warranty tracker
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
        'quantity'        => 'integer',
    ];

    // ─── Media Library Collections ──────────────────────────────────────────
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('asset_photos')
             ->useDisk('public')
             ->singleFile();  // Only one primary photo per asset
    }

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

    // ─── Relationships ────────────────────────────────────────────────────────
    public function placements()
    {
        // Orders by newest first so we see the current location at the top
        return $this->hasMany(AssetPlacement::class)->orderBy('assigned_date', 'desc');
    }

    public function damageReports()
    {
        return $this->hasMany(DamageReport::class)->latest();
    }

    public function inspections()
    {
        return $this->hasMany(AssetInspection::class)->latest('inspection_date');
    }

    public function upgrades()
    {
        return $this->hasMany(AssetUpgrade::class)->orderBy('date', 'desc');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class)->latest('maintenance_date');
    }

    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class)->latest();
    }

    public function lossReports()
    {
        return $this->hasMany(AssetLossReport::class)->latest();
    }

    public function transfers()
    {
        return $this->hasMany(AssetTransfer::class)->latest('transfer_date');
    }

    public function vehicleDisposalAssessment()
    {
        return $this->hasOne(VehicleDisposalAssessment::class);
    }

    // ─── Accessor: Get primary photo URL ──────────────────────────────────────
    public function getPrimaryPhotoUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('asset_photos');

        return $media ? $media->getUrl() : $this->image_url;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "{$this->name ?? $this->asset_tag ?? '#' . $this->id} has been {$eventName}");
    }
}

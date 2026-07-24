<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetMaintenance extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'description',
        'contract_no',
        'company_name',
        'cost',
        'status',
        'notes',
        'signatures',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost'             => 'decimal:2',
        'signatures'       => 'array',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

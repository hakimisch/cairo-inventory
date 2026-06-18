<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class VehicleDisposalAssessment extends Model
{
    use LogsActivity;
    protected $fillable = [
        'asset_id',
        'plate_no',
        'chassis_no',
        'engine_no',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_year',
        'road_tax_expiry',
        'engine_capacity',
        'fuel_type',
        'vehicle_color',
        'condition_report',
        'estimated_value',
        'assessment_date',
        'assessor_name',
        'assessor_position',
        'recommendation',
        'status',
        'notes',
    ];

    protected $casts = [
        'road_tax_expiry'  => 'date',
        'assessment_date'  => 'date',
        'vehicle_year'     => 'integer',
        'estimated_value'  => 'decimal:2',
    ];

    /**
     * Get the asset being assessed for disposal.
     */
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

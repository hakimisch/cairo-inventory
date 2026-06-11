<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetInspection extends Model
{
    use LogsActivity;
    protected $fillable = [
        'asset_id',
        'inspection_date',
        'status',
        'inspector_name',
        'notes',
        // NEW — KEW.PA-10/PA-11 compliance fields
        'is_record_complete',
        'is_record_updated',
        'actual_location',
        'actual_quantity',
        'signatures',
    ];

    protected $casts = [
        'inspection_date'    => 'date',
        'is_record_complete' => 'boolean',
        'is_record_updated'  => 'boolean',
        'actual_quantity'    => 'integer',
        'signatures'         => 'array',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

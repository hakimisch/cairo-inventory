<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetPlacement extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'asset_id', 
        'custodian_name', 
        'staff_id',
        'borrower_phone',
        'matric_no',
        'authorizer_name',
        'location', 
        'quantity_placed',
        'specific_serial_no',
        'is_lokasi_luar', 
        'assigned_date', 
        'expected_return_date', 
        'returned_date'
    ];
    
    protected $casts = [
        'assigned_date' => 'date', 
        'expected_return_date' => 'date', 
        'returned_date' => 'date', 
        'is_lokasi_luar' => 'boolean',
        'quantity_placed' => 'integer',
    ];

    public function asset() { return $this->belongsTo(Asset::class); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetInspection extends Model
{
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
    ];

    protected $casts = [
        'inspection_date'    => 'date',
        'is_record_complete' => 'boolean',
        'is_record_updated'  => 'boolean',
        'actual_quantity'    => 'integer',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}

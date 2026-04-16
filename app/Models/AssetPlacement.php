<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetPlacement extends Model
{
    protected $fillable = [
        'asset_id', 
        'custodian_name', 
        'staff_id',             // NEW
        'location', 
        'quantity_placed',      // NEW
        'specific_serial_no',   // NEW
        'is_lokasi_luar', 
        'assigned_date', 
        'expected_return_date', 
        'returned_date'
    ];
    
    protected $casts = [
        'assigned_date' => 'date', 
        'expected_return_date' => 'date', 
        'returned_date' => 'date', 
        'is_lokasi_luar' => 'boolean'
    ];

    public function asset() { return $this->belongsTo(Asset::class); }
}
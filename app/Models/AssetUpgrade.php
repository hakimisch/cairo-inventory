<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetUpgrade extends Model
{
    protected $fillable = [
        'asset_id', 
        'date', 
        'description', 
        'warranty_period', 
        'cost'
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
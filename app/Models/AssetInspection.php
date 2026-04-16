<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetInspection extends Model
{
    protected $fillable = ['asset_id', 'inspection_date', 'status', 'inspector_name', 'notes'];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
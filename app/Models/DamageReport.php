<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamageReport extends Model
{
    // These fields allow "Mass Assignment"
    protected $fillable = [
        'asset_id', 
        'reported_by', 
        'damage_date', 
        'last_user', 
        'previous_maintenance_cost', 
        'damage_description', 
        'status',
        'technical_notes', 
        'recommendation', 
        'hod_decision'
    ];

    // This ensures the date is treated as a Carbon instance
    protected $casts = [
        'damage_date' => 'date',
        'previous_maintenance_cost' => 'decimal:2',
    ];

    /**
     * Get the asset that owns the damage report.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
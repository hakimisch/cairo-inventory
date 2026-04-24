<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AssetDisposal extends Model
{
    protected $fillable = [
        'asset_id',
        'request_reason',
        'committee_decision',
        'disposal_method', // Tanam, Bakar, Tenggelam, Jualan, Pindahan
        'disposal_date',
        'approval_reference',
        'status',          // draft, committee_review, approved, completed, cancelled
        'notes',
    ];

    protected $casts = [
        'disposal_date' => 'date',
    ];

    /**
     * Get the asset being disposed.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get all committee appointments for this disposal (PA-15).
     */
    public function committeeAppointments(): MorphMany
    {
        return $this->morphMany(CommitteeAppointment::class, 'appointable');
    }
}

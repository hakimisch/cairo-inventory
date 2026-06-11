<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetDisposal extends Model
{
    use LogsActivity;
    protected $fillable = [
        'asset_id',
        'request_reason',
        'committee_decision',
        'disposal_method', // Tanam, Bakar, Tenggelam, Jualan, Pindahan
        'disposal_date',
        'approval_reference',
        'status',          // draft, committee_review, approved, completed, cancelled
        'notes',
        'signatures',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'signatures'   => 'array',
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

    /**
     * Get all disposal sales (PA-21→27A) for this disposal.
     */
    public function disposalSales(): HasMany
    {
        return $this->hasMany(DisposalSale::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

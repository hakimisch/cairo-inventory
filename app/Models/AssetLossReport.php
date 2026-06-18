<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetLossReport extends Model
{
    use LogsActivity;
    protected $fillable = [
        'asset_id',
        'incident_location',
        'loss_date',
        'loss_method',
        'last_officer',
        'police_report_no',
        'current_value',
        'action_type',        // surcharge | write_off
        'write_off_value',
        'surcharge_amount',
        'applied_date',
        'investigation_summary', // PA-30
        'approval_reference',    // PA-31
        'status',                // under_investigation, committee_review, approved, closed
        'notes',
    ];

    protected $casts = [
        'loss_date'     => 'date',
        'applied_date'  => 'date',
        'current_value' => 'decimal:2',
        'write_off_value'  => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
    ];

    /**
     * Get the asset that was lost.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get all committee appointments for this loss investigation (PA-29).
     */
    public function committeeAppointments(): MorphMany
    {
        return $this->morphMany(CommitteeAppointment::class, 'appointable');
    }

    /**
     * Get the final loss investigation report (PA-30).
     */
    public function finalLossReport(): HasOne
    {
        return $this->hasOne(FinalLossReport::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

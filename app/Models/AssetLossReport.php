<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AssetLossReport extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'incident_location',
        'loss_date',
        'loss_method',
        'last_officer',
        'police_report_no',
        'current_value',
        'action_type',
        'write_off_value',
        'surcharge_amount',
        'applied_date',
        'investigation_summary',
        'approval_reference',
        'status',
        'notes',
    ];

    protected $casts = [
        'loss_date'     => 'date',
        'applied_date'  => 'date',
        'current_value' => 'decimal:2',
        'write_off_value'  => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function committeeAppointments(): MorphMany
    {
        return $this->morphMany(CommitteeAppointment::class, 'appointable');
    }

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

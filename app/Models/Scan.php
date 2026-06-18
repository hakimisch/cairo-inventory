<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Scan extends Model
{
    use LogsActivity;

    protected $fillable = [
        'serial_number',
        'scan_type',
        'scanner_user_id',
        'scanned_at',
        'do_line_item_id',
        'asset_id',
        'location',
        'result',
        'notes',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function scanner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanner_user_id');
    }

    public function doLineItem(): BelongsTo
    {
        return $this->belongsTo(DoLineItem::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ─── Activity Log ──────────────────────────────────────────────────────────

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}

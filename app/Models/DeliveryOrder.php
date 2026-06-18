<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DeliveryOrder extends Model implements HasMedia
{
    use LogsActivity, InteractsWithMedia;

    protected $fillable = [
        'do_no',
        'supplier_id',
        'ack_date',
        'po_reference',
        'sales_rep',
        'terms',
        'total_pages',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'ack_date'    => 'date',
        'total_pages' => 'integer',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────────

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(DoLineItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'partial']);
    }

    public function scopeComplete($query)
    {
        return $query->where('status', 'complete');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    // ─── Media Library ────────────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('do_documents')
             ->useDisk('public')
             ->acceptsFile(fn ($file) => in_array($file->mimeType, [
                 'application/pdf',
                 'image/jpeg',
                 'image/png',
                 'image/jpg',
             ]));
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

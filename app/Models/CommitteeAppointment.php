<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CommitteeAppointment extends Model
{
    protected $fillable = [
        'user_id',
        'appointable_type',
        'appointable_id',
        'role',
        'valid_from',
        'valid_until',
        'notes',
    ];

    protected $casts = [
        'valid_from'  => 'date',
        'valid_until' => 'date',
    ];

    /**
     * Get the user (committee member).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent appointable model (disposal or loss report).
     */
    public function appointable(): MorphTo
    {
        return $this->morphTo();
    }
}

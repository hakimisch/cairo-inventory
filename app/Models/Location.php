<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Location
 *
 * Reference table of physical locations (labs, offices, rooms) where
 * assets can be placed. Used for dropdown selection in verify/receive
 * modals and throughout the PA form workflow.
 *
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Location extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];
}

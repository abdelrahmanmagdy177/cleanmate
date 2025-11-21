<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Timeslot extends Model
{
    protected $fillable = ['day', 'start_time', 'end_time', 'capacity', 'is_active'];

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'timeslot_orders')
            ->withPivot('date')
            ->withTimestamps();
    }
}

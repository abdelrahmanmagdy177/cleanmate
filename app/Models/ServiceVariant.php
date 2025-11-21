<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceVariant extends Model
{
    protected $fillable = ['service_id', 'name', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ServicePrice::class);
    }
}

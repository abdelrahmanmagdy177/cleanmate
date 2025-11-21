<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasManyThrough(CustomerAddress::class, Area::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

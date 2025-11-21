<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePrice extends Model
{
    protected $fillable = ['service_variant_id', 'min_space', 'max_space', 'price', 'area_id'];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}

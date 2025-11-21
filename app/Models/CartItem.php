<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'customer_id',
        'service_id',
        'variant_id',
        'customer_address_id',
        'space',
        'notes',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    /**
     * Calculate the price for this cart item.
     */
    public function calculatePrice(): ?float
    {
        $area = $this->address->area;
        
        if (!$area) {
            return null;
        }

        $priceModel = $this->variant->prices()
            ->where('area_id', $area->id)
            ->where('min_space', '<=', $this->space)
            ->where(function ($query) {
                $query->where('max_space', '>=', $this->space)
                      ->orWhereNull('max_space');
            })
            ->orderBy('min_space', 'desc')
            ->first();

        if (!$priceModel) {
            return null;
        }

        $servicePrice = $priceModel->price;
        $vatRate = config('cleanmate.vat_rate', 14.00);
        $vatAmount = round(($servicePrice * $vatRate) / 100, 2);
        
        return $servicePrice + $vatAmount;
    }
}

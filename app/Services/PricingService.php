<?php

namespace App\Services;

use App\Models\ServiceVariant;
use App\Models\CartItem;

class PricingService
{
    /**
     * Calculate price for a cart item.
     */
    public function calculateCartItemPrice(CartItem $item): ?float
    {
        $area = $item->address->area;
        
        if (!$area) {
            return null;
        }

        $priceModel = $item->variant->prices()
            ->where('area_id', $area->id)
            ->where('min_space', '<=', $item->space)
            ->where(function ($query) use ($item) {
                $query->where('max_space', '>=', $item->space)
                      ->orWhereNull('max_space');
            })
            ->orderBy('min_space', 'desc')
            ->first();

        if (!$priceModel) {
            return null;
        }

        return $this->calculateTotalWithVAT($priceModel->price);
    }

    /**
     * Find price for a variant and space in a specific area.
     */
    public function findPrice(ServiceVariant $variant, int $space, int $areaId): ?float
    {
        $priceModel = $variant->prices()
            ->where('area_id', $areaId)
            ->where('min_space', '<=', $space)
            ->where(function ($query) use ($space) {
                $query->where('max_space', '>=', $space)
                      ->orWhereNull('max_space');
            })
            ->orderBy('min_space', 'desc')
            ->first();

        return $priceModel ? $priceModel->price : null;
    }

    /**
     * Calculate total price with VAT.
     */
    public function calculateTotalWithVAT(float $servicePrice): float
    {
        $vatRate = config('cleanmate.vat_rate', 14.00);
        $vatAmount = round(($servicePrice * $vatRate) / 100, 2);
        
        return $servicePrice + $vatAmount;
    }

    /**
     * Calculate VAT amount.
     */
    public function calculateVAT(float $servicePrice): float
    {
        $vatRate = config('cleanmate.vat_rate', 14.00);
        return round(($servicePrice * $vatRate) / 100, 2);
    }

    /**
     * Get price breakdown.
     */
    public function getPriceBreakdown(float $servicePrice): array
    {
        $vatRate = config('cleanmate.vat_rate', 14.00);
        $vatAmount = $this->calculateVAT($servicePrice);
        $totalPrice = $servicePrice + $vatAmount;

        return [
            'service_price' => $servicePrice,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_price' => $totalPrice,
        ];
    }
}

<?php

namespace App\Services\Customer;

use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Service;
use App\Models\Timeslot;
use App\Models\TimeslotOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Get all cart items for a customer.
     */
    public function getCartItems(int $customerId): Collection
    {
        return CartItem::where('customer_id', $customerId)
            ->with(['service', 'variant', 'address'])
            ->get();
    }

    /**
     * Add item to cart with validation.
     */
    public function addToCart(int $customerId, array $data): CartItem
    {
        // Validate address belongs to customer
        $address = CustomerAddress::where('id', $data['customer_address_id'])
            ->where('customer_id', $customerId)
            ->first();
            
        if (!$address) {
            throw ValidationException::withMessages(['customer_address_id' => 'Address does not belong to this customer.']);
        }

        // Validate area
        if (!$address->area_id) {
            throw ValidationException::withMessages(['customer_address_id' => 'This address is not assigned to any service area.']);
        }

        $area = $address->area;
        if (!$area || !$area->is_active) {
            throw ValidationException::withMessages(['customer_address_id' => 'This area is currently not available for service.']);
        }

        $zone = $area->zone;
        if (!$zone || !$zone->is_active) {
            throw ValidationException::withMessages(['customer_address_id' => 'This zone is currently not available for service.']);
        }

        // Validate service belongs to area
        $service = Service::find($data['service_id']);
        if (!$service || $service->area_id !== $area->id) {
            throw ValidationException::withMessages(['service_id' => 'The selected service is not available in your area.']);
        }

        // Create cart item
        return CartItem::create([
            'customer_id' => $customerId,
            'service_id' => $data['service_id'],
            'variant_id' => $data['variant_id'],
            'customer_address_id' => $data['customer_address_id'],
            'space' => $data['space'],
            'notes' => $data['notes'] ?? null,
        ]);
    }

    /**
     * Update cart item.
     */
    public function updateCartItem(int $itemId, int $customerId, array $data): ?CartItem
    {
        $cartItem = CartItem::where('id', $itemId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$cartItem) {
            return null;
        }

        $cartItem->update($data);
        return $cartItem->fresh();
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $itemId, int $customerId): bool
    {
        $cartItem = CartItem::where('id', $itemId)
            ->where('customer_id', $customerId)
            ->first();

        if (!$cartItem) {
            return false;
        }

        return $cartItem->delete();
    }

    /**
     * Clear all cart items for customer.
     */
    public function clearCart(int $customerId): int
    {
        return CartItem::where('customer_id', $customerId)->delete();
    }

    /**
     * Calculate cart total with price breakdown.
     */
    public function calculateCartTotal(int $customerId): array
    {
        $cartItems = $this->getCartItems($customerId);
        
        $subtotal = 0;
        $items = [];

        foreach ($cartItems as $item) {
            $price = $item->calculatePrice();
            
            if ($price !== null) {
                $subtotal += $price;
                $items[] = [
                    'id' => $item->id,
                    'service' => $item->service->name,
                    'variant' => $item->variant->name,
                    'price' => $price,
                ];
            }
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'item_count' => $cartItems->count(),
        ];
    }
}

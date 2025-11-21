<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CartService;
use App\Http\Requests\Customer\StoreCartItemRequest;
use App\Http\Requests\Customer\UpdateCartItemRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get all cart items for authenticated customer.
     */
    public function index(Request $request)
    {
        $customer = $request->user();
        $cartItems = $this->cartService->getCartItems($customer->id);
        $total = $this->cartService->calculateCartTotal($customer->id);

        return response()->json([
            'cart_items' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function store(StoreCartItemRequest $request)
    {
        try {
            $customer = $request->user();
            $cartItem = $this->cartService->addToCart($customer->id, $request->validated());

            return response()->json([
                'message' => 'Item added to cart successfully',
                'data' => $cartItem->load(['service', 'variant', 'address']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Update cart item.
     */
    public function update(UpdateCartItemRequest $request, $id)
    {
        $customer = $request->user();
        $cartItem = $this->cartService->updateCartItem($id, $customer->id, $request->validated());

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        return response()->json([
            'message' => 'Cart item updated successfully',
            'data' => $cartItem->load(['service', 'variant', 'address']),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, $id)
    {
        $customer = $request->user();
        $deleted = $this->cartService->removeFromCart($id, $customer->id);

        if (!$deleted) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        return response()->json(['message' => 'Item removed from cart successfully']);
    }

    /**
     * Clear all cart items.
     */
    public function clear(Request $request)
    {
        $customer = $request->user();
        $count = $this->cartService->clearCart($customer->id);

        return response()->json([
            'message' => 'Cart cleared successfully',
            'items_removed' => $count,
        ]);
    }
}

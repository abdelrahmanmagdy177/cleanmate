<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'variant_id' => 'required|exists:service_variants,id',
            'customer_address_id' => 'required|exists:customer_addresses,id',
            'space' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $customer = $request->user();
            $cartItem = $this->cartService->addToCart($customer->id, $request->all());

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
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'space' => 'sometimes|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();
        $cartItem = $this->cartService->updateCartItem($id, $customer->id, $request->all());

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

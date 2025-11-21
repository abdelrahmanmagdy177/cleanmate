<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function getTimeslots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
            'area_id' => 'required|exists:areas,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $availableSlots = $this->orderService->getAvailableTimeslots($request->date, $request->area_id);
        
        return response()->json(['data' => $availableSlots]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'service_id' => 'required|exists:services,id',
            'variant_id' => 'required|exists:service_variants,id',
            'timeslot_id' => 'required|exists:timeslots,id',
            'customer_address_id' => 'required|exists:customer_addresses,id',
            'space' => 'required|integer|min:1',
            'order_date' => 'required|date|after_or_equal:today',
            'payment_method' => 'required|in:cash,credit',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $order = $this->orderService->createOrder($request->all());

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load(['customer', 'service', 'variant', 'timeslots']),
                'price_breakdown' => $order->getPriceBreakdown(),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422); // Or 422 based on preference, usually 422 for validation
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while processing your order.'], 500);
        }
    }
}

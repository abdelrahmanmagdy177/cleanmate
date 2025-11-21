<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\OrderService;
use App\Enums\OrderStatusClassification;
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

    /**
     * Get all orders for authenticated customer.
     * Optionally filter by classification (processing, finished) or specific status.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'classification' => 'nullable|string|' . OrderStatusClassification::getValidationRule(),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();
        $classification = $request->query('classification');
        
        $orders = $this->orderService->getOrdersByClassification($customer->id, $classification);

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Get processing orders (pending, assigned, in_progress).
     */
    public function processing(Request $request)
    {
        $customer = $request->user();
        $orders = $this->orderService->getProcessingOrders($customer->id);

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Get finished orders (completed, cancelled).
     */
    public function finished(Request $request)
    {
        $customer = $request->user();
        $orders = $this->orderService->getFinishedOrders($customer->id);

        return response()->json([
            'data' => $orders,
        ]);
    }

    /**
     * Get a single order by ID.
     */
    public function show(Request $request, $id)
    {
        $customer = $request->user();
        $order = $this->orderService->getOrderById($customer->id, $id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'data' => $order,
            'price_breakdown' => $order->getPriceBreakdown(),
        ]);
    }
}

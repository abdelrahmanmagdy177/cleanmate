<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function getTimeslots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $date = $request->date;
        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
        
        $timeslots = \App\Models\Timeslot::where('is_active', true)
            ->where('day', $dayOfWeek)
            ->get();
            
        $availableSlots = [];

        foreach ($timeslots as $slot) {
            $bookedCount = \App\Models\TimeslotOrder::where('timeslot_id', $slot->id)
                ->where('date', $date)
                ->count();

            if ($bookedCount < $slot->capacity) {
                $availableSlots[] = [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'remaining_capacity' => $slot->capacity - $bookedCount
                ];
            }
        }
        
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

        // Check capacity
        $timeslot = \App\Models\Timeslot::find($request->timeslot_id);
        $bookedCount = \App\Models\TimeslotOrder::where('timeslot_id', $timeslot->id)
            ->where('date', $request->order_date)
            ->count();

        if ($bookedCount >= $timeslot->capacity) {
            return response()->json(['error' => 'Selected timeslot is fully booked.'], 422);
        }

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['email' => $request->customer_email],
            [
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'status' => 'active'
            ]
        );
        
        // Verify address belongs to customer
        $address = \App\Models\CustomerAddress::where('id', $request->customer_address_id)
            ->where('customer_id', $customer->id)
            ->first();
            
        if (!$address) {
             return response()->json(['error' => 'Address does not belong to this customer.'], 422);
        }

        // Validate that address has an active area in an active zone
        if (!$address->area_id) {
            return response()->json([
                'error' => 'This address is not assigned to any service area. Please contact support or choose a different address.'
            ], 422);
        }

        $area = $address->area;
        if (!$area || !$area->is_active) {
            return response()->json([
                'error' => 'This area is currently not available for service. Please choose a different address.'
            ], 422);
        }

        $zone = $area->zone;
        if (!$zone || !$zone->is_active) {
            return response()->json([
                'error' => 'This zone is currently not available for service. Please choose a different address.'
            ], 422);
        }

        // Calculate total price
        $variant = ServiceVariant::find($request->variant_id);
        
        // Find matching price for the space
        $priceModel = $variant->prices()
            ->where('min_space', '<=', $request->space)
            ->where(function ($query) use ($request) {
                $query->where('max_space', '>=', $request->space)
                      ->orWhereNull('max_space');
            })
            ->orderBy('min_space', 'desc') // Get the most specific range (highest min_space that fits)
            ->first();

        if (!$priceModel) {
            return response()->json(['error' => 'No pricing available for this space size.'], 422);
        }

        $price = $priceModel->price;

        // Create order with pricing calculation
        $order = new Order([
            'customer_id' => $customer->id,
            'customer_address_id' => $request->customer_address_id,
            'service_id' => $request->service_id,
            'variant_id' => $request->variant_id,
            'order_date' => $request->order_date,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'pending',
            'notes' => $request->notes,
        ]);

        // Calculate all pricing components
        $order->calculatePricing($price);
        $order->save();

        // Link timeslot
        \App\Models\TimeslotOrder::create([
            'timeslot_id' => $request->timeslot_id,
            'order_id' => $order->id,
            'date' => $request->order_date,
        ]);

        return response()->json([
            'message' => 'Order created successfully',
            'data' => $order->load(['customer', 'service', 'variant', 'timeslots']),
            'price_breakdown' => $order->getPriceBreakdown(),
        ], 201);
    }
}

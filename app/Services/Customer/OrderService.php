<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceVariant;
use App\Models\Timeslot;
use App\Models\TimeslotOrder;
use App\Models\CustomerAddress;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Get available timeslots for a specific date and area.
     *
     * @param string $date
     * @param int $areaId
     * @return array
     */
    public function getAvailableTimeslots(string $date, int $areaId): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        $timeslots = Timeslot::where('is_active', true)
            ->where('day', $dayOfWeek)
            ->where('area_id', $areaId)
            ->get();
            
        $availableSlots = [];

        foreach ($timeslots as $slot) {
            $bookedCount = TimeslotOrder::where('timeslot_id', $slot->id)
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
        
        return $availableSlots;
    }

    /**
     * Create a new order.
     *
     * @param array $data
     * @return Order
     * @throws ValidationException
     */
    public function createOrder(array $data): Order
    {
        // Check capacity
        $timeslot = Timeslot::find($data['timeslot_id']);
        $bookedCount = TimeslotOrder::where('timeslot_id', $timeslot->id)
            ->where('date', $data['order_date'])
            ->count();

        if ($bookedCount >= $timeslot->capacity) {
            throw ValidationException::withMessages(['timeslot_id' => 'Selected timeslot is fully booked.']);
        }

        // Find or create customer
        $customer = Customer::firstOrCreate(
            ['email' => $data['customer_email']],
            [
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
                'status' => 'active'
            ]
        );
        
        // Verify address belongs to customer
        $address = CustomerAddress::where('id', $data['customer_address_id'])
            ->where('customer_id', $customer->id)
            ->first();
            
        if (!$address) {
             throw ValidationException::withMessages(['customer_address_id' => 'Address does not belong to this customer.']);
        }

        // Validate that address has an active area in an active zone
        if (!$address->area_id) {
            throw ValidationException::withMessages(['customer_address_id' => 'This address is not assigned to any service area. Please contact support or choose a different address.']);
        }

        $area = $address->area;
        if (!$area || !$area->is_active) {
            throw ValidationException::withMessages(['customer_address_id' => 'This area is currently not available for service. Please choose a different address.']);
        }

        $zone = $area->zone;
        if (!$zone || !$zone->is_active) {
            throw ValidationException::withMessages(['customer_address_id' => 'This zone is currently not available for service. Please choose a different address.']);
        }

        // Validate timeslot belongs to the area
        if ($timeslot->area_id !== $area->id) {
            throw ValidationException::withMessages(['timeslot_id' => 'The selected timeslot is not available for your area.']);
        }

        // Validate service is available in the area
        $service = \App\Models\Service::find($data['service_id']);
        if (!$service || $service->area_id !== $area->id) {
            throw ValidationException::withMessages(['service_id' => 'The selected service is not available in your area.']);
        }

        // Calculate total price
        $variant = ServiceVariant::find($data['variant_id']);
        
        // Find matching price for the space and area
        $priceModel = $variant->prices()
            ->where('area_id', $area->id)
            ->where('min_space', '<=', $data['space'])
            ->where(function ($query) use ($data) {
                $query->where('max_space', '>=', $data['space'])
                      ->orWhereNull('max_space');
            })
            ->orderBy('min_space', 'desc') // Get the most specific range (highest min_space that fits)
            ->first();

        if (!$priceModel) {
            throw ValidationException::withMessages(['space' => 'No pricing available for this space size.']);
        }

        $price = $priceModel->price;

        // Create order with pricing calculation
        $order = new Order([
            'customer_id' => $customer->id,
            'customer_address_id' => $data['customer_address_id'],
            'service_id' => $data['service_id'],
            'variant_id' => $data['variant_id'],
            'order_date' => $data['order_date'],
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
            'payment_status' => $data['payment_method'] === 'cash' ? 'pending' : 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        // Calculate all pricing components
        $order->calculatePricing($price);
        $order->save();

        // Link timeslot
        TimeslotOrder::create([
            'timeslot_id' => $data['timeslot_id'],
            'order_id' => $order->id,
            'date' => $data['order_date'],
        ]);

        return $order;
    }
}

<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Models\ServiceVariant;
use App\Models\Timeslot;
use App\Models\TimeslotOrder;
use App\Models\CustomerAddress;
use App\Services\TimeslotService;
use App\Services\PricingService;
use App\Services\CustomerService;
use App\Services\OrderQueryService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class OrderService
{
    protected TimeslotService $timeslotService;
    protected PricingService $pricingService;
    protected CustomerService $customerService;
    protected OrderQueryService $orderQueryService;

    public function __construct(
        TimeslotService $timeslotService,
        PricingService $pricingService,
        CustomerService $customerService,
        OrderQueryService $orderQueryService
    ) {
        $this->timeslotService = $timeslotService;
        $this->pricingService = $pricingService;
        $this->customerService = $customerService;
        $this->orderQueryService = $orderQueryService;
    }

    /**
     * Get available timeslots for a specific date and area.
     */
    public function getAvailableTimeslots(string $date, int $areaId): array
    {
        return $this->timeslotService->getAvailableTimeslots($date, $areaId);
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
        $customer = $this->customerService->findOrCreateByEmail(
            $data['customer_email'],
            [
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone']
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
        $servicePrice = $this->pricingService->findPrice($variant, $data['space'], $area->id);
        
        if (!$servicePrice) {
            throw ValidationException::withMessages(['space' => 'No pricing available for the selected space.']);
        }

        $totalPrice = $this->pricingService->calculateTotalWithVAT($servicePrice);

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
        $order->calculatePricing($servicePrice);
        $order->save();

        // Link timeslot
        TimeslotOrder::create([
            'timeslot_id' => $data['timeslot_id'],
            'order_id' => $order->id,
            'date' => $data['order_date'],
        ]);

        return $order;
    }

    /**
     * Get all orders for a customer.
     */
    public function getCustomerOrders(int $customerId)
    {
        return $this->orderQueryService->getCustomerOrders($customerId);
    }

    /**
     * Get processing orders (pending, assigned, in_progress).
     */
    public function getProcessingOrders(int $customerId)
    {
        return $this->orderQueryService->getProcessingOrders($customerId);
    }

    /**
     * Get finished orders (completed, cancelled).
     */
    public function getFinishedOrders(int $customerId)
    {
        return $this->orderQueryService->getFinishedOrders($customerId);
    }

    /**
     * Get a single order by ID for a customer.
     */
    public function getOrderById(int $customerId, int $orderId): ?Order
    {
        return $this->orderQueryService->getOrderById($customerId, $orderId);
    }

    /**
     * Get orders by classification or specific status.
     * 
     * @param int $customerId
     * @param string|null $classification Can be 'processing', 'finished', or any specific status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersByClassification(int $customerId, ?string $classification = null)
    {
        return $this->orderQueryService->getOrdersByClassification($customerId, $classification);
    }
}

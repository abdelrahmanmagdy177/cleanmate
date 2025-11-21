<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\OrderStatusClassification;
use Illuminate\Database\Eloquent\Collection;

class OrderQueryService
{
    /**
     * Get all orders for a customer.
     */
    public function getCustomerOrders(int $customerId): Collection
    {
        return Order::where('customer_id', $customerId)
            ->with(['service', 'variant', 'customerAddress', 'timeslots'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get processing orders (pending, assigned, in_progress).
     */
    public function getProcessingOrders(int $customerId): Collection
    {
        return Order::where('customer_id', $customerId)
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->with(['service', 'variant', 'customerAddress', 'timeslots'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get finished orders (completed, cancelled).
     */
    public function getFinishedOrders(int $customerId): Collection
    {
        return Order::where('customer_id', $customerId)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['service', 'variant', 'customerAddress', 'timeslots'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get a single order by ID for a customer.
     */
    public function getOrderById(int $customerId, int $orderId): ?Order
    {
        return Order::where('customer_id', $customerId)
            ->where('id', $orderId)
            ->with(['service', 'variant', 'customerAddress', 'timeslots', 'workers'])
            ->first();
    }

    /**
     * Get orders by status.
     */
    public function getOrdersByStatus(int $customerId, string $status): Collection
    {
        return Order::where('customer_id', $customerId)
            ->where('status', $status)
            ->with(['service', 'variant', 'customerAddress', 'timeslots'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get orders by classification or specific status.
     * 
     * @param int $customerId
     * @param string|null $classification Can be 'processing', 'finished', or any specific status
     * @return Collection
     */
    public function getOrdersByClassification(int $customerId, ?string $classification = null): Collection
    {
        $query = Order::where('customer_id', $customerId)
            ->with(['service', 'variant', 'customerAddress', 'timeslots']);

        if ($classification) {
            // Check if it's a classification group (e.g., 'processing', 'finished')
            $statuses = OrderStatusClassification::getStatuses($classification);
            
            if ($statuses) {
                // It's a classification group, filter by multiple statuses
                $query->whereIn('status', $statuses);
            } else {
                // It's a specific status, filter by single status
                $query->where('status', $classification);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}

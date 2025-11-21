<?php

namespace App\Services\Worker;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Get orders assigned to the worker.
     *
     * @param Worker $worker
     * @return Collection
     */
    public function getWorkerOrders(Worker $worker): Collection
    {
        return $worker->orders()
            ->with(['customer', 'customerAddress', 'service', 'variant'])
            ->orderBy('assigned_at', 'desc')
            ->get();
    }

    /**
     * Update the status of an assigned order.
     *
     * @param Worker $worker
     * @param int $orderId
     * @param string $status
     * @return void
     * @throws ValidationException
     */
    public function updateOrderStatus(Worker $worker, int $orderId, string $status): void
    {
        $order = $worker->orders()->where('order_id', $orderId)->first();

        if (!$order) {
            throw ValidationException::withMessages(['order_id' => 'Order not found or not assigned to you']);
        }

        // Update Order status and history
        $order->updateStatus($status, 'worker', $worker->id);

        // Update Pivot status
        // Logic from controller: worker_on_way -> accepted, completed -> completed, else accepted
        $pivotStatus = $status == 'worker_on_way' ? 'accepted' : ($status == 'completed' ? 'completed' : 'accepted');
        
        $order->pivot->status = $pivotStatus;
        $order->pivot->save();
    }
}

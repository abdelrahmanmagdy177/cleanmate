<?php

namespace App\Services\Admin;

use App\Models\Worker;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class WorkerService
{
    /**
     * Get all workers.
     *
     * @return Collection
     */
    public function getAllWorkers(): Collection
    {
        return Worker::all();
    }

    /**
     * Create a new worker.
     *
     * @param array $data
     * @return Worker
     */
    public function createWorker(array $data): Worker
    {
        return Worker::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'status' => 'active',
        ]);
    }

    /**
     * Get a worker by ID.
     *
     * @param int $id
     * @return Worker|null
     */
    public function getWorker(int $id): ?Worker
    {
        return Worker::find($id);
    }

    /**
     * Update a worker.
     *
     * @param int $id
     * @param array $data
     * @return Worker|null
     */
    public function updateWorker(int $id, array $data): ?Worker
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return null;
        }

        $updateData = collect($data)->only(['name', 'email', 'phone', 'status'])->toArray();
        
        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $worker->update($updateData);

        return $worker;
    }

    /**
     * Delete a worker.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWorker(int $id): bool
    {
        $worker = Worker::find($id);

        if (!$worker) {
            return false;
        }

        return $worker->delete();
    }

    /**
     * Assign a worker to an order.
     *
     * @param int $orderId
     * @param int $workerId
     * @return void
     * @throws ValidationException
     */
    public function assignOrder(int $orderId, int $workerId): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            throw ValidationException::withMessages(['order_id' => 'Order not found']);
        }

        $worker = Worker::find($workerId);
        
        if (!$worker) {
             throw ValidationException::withMessages(['worker_id' => 'Worker not found']);
        }

        // Attach worker to order if not already attached
        if (!$order->workers()->where('worker_id', $worker->id)->exists()) {
            $order->workers()->attach($worker->id, ['status' => 'assigned']);
        }

        // Update order status
        $order->updateStatus('assigned', 'system');
    }
}

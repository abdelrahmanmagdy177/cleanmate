<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public string $newStatus;
    public string $updatedByType;
    public ?int $updatedById;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus, string $updatedByType, ?int $updatedById = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->updatedByType = $updatedByType;
        $this->updatedById = $updatedById;
    }
}

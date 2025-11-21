<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Models\Notification;

class SendOrderStatusNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusUpdated $event): void
    {
        // Only send notifications for specific status changes
        $notifiableStatuses = ['worker_on_way', 'in_progress', 'completed'];
        
        if (!in_array($event->newStatus, $notifiableStatuses)) {
            return;
        }

        // Get notification title and message based on status
        $notifications = [
            'worker_on_way' => [
                'title' => 'Worker is on the way!',
                'message' => 'Your assigned worker is on the way to your location. They should arrive soon.',
            ],
            'in_progress' => [
                'title' => 'Service in progress',
                'message' => 'Your service has started. Our worker is now working on your order.',
            ],
            'completed' => [
                'title' => 'Service completed!',
                'message' => 'Your service has been completed successfully. Thank you for choosing CleanMate!',
            ],
        ];

        $notificationData = $notifications[$event->newStatus];

        // Create notification for customer
        Notification::create([
            'customer_id' => $event->order->customer_id,
            'order_id' => $event->order->id,
            'type' => 'order_status_update',
            'title' => $notificationData['title'],
            'message' => $notificationData['message'],
            'data' => [
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'updated_by_type' => $event->updatedByType,
                'updated_by_id' => $event->updatedById,
            ],
        ]);
    }
}

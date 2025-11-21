<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Worker;
use App\Models\Service;
use App\Models\ServiceVariant;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_created_when_order_status_updated()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '123456',
            'status' => 'active',
        ]);

        $address = $customer->addresses()->create([
            'name' => 'Home',
            'title' => 'Test',
            'address_details' => '123 Test St',
        ]);

        $service = Service::create(['name' => 'S', 'description' => 'D', 'active' => true]);
        $variant = ServiceVariant::create(['service_id' => $service->id, 'name' => 'V', 'description' => 'D']);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_address_id' => $address->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'assigned',
            'total_price' => 100,
        ]);

        // Update status to worker_on_way
        $order->updateStatus('worker_on_way', 'worker', 1);

        // Check notification was created
        $this->assertDatabaseHas('notifications', [
            'customer_id' => $customer->id,
            'order_id' => $order->id,
            'type' => 'order_status_update',
        ]);

        $notification = $customer->notifications()->first();
        $this->assertEquals('Worker is on the way!', $notification->title);
        $this->assertNull($notification->read_at);
    }

    public function test_customer_can_view_notifications()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '123456',
            'status' => 'active',
        ]);

        $address = $customer->addresses()->create([
            'name' => 'Home',
            'title' => 'Test',
            'address_details' => '123 Test St',
        ]);

        $service = Service::create(['name' => 'S', 'description' => 'D', 'active' => true]);
        $variant = ServiceVariant::create(['service_id' => $service->id, 'name' => 'V', 'description' => 'D']);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_address_id' => $address->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'assigned',
            'total_price' => 100,
        ]);

        $order->updateStatus('worker_on_way', 'worker', 1);

        $response = $this->getJson('/api/customer/notifications?customer_email=customer@test.com');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'unread_count'])
            ->assertJsonFragment(['unread_count' => 1]);
    }

    public function test_customer_can_mark_notification_as_read()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '123456',
            'status' => 'active',
        ]);

        $address = $customer->addresses()->create([
            'name' => 'Home',
            'title' => 'Test',
            'address_details' => '123 Test St',
        ]);

        $service = Service::create(['name' => 'S', 'description' => 'D', 'active' => true]);
        $variant = ServiceVariant::create(['service_id' => $service->id, 'name' => 'V', 'description' => 'D']);

        $order = Order::create([
            'customer_id' => $customer->id,
            'customer_address_id' => $address->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $notification = $customer->notifications()->create([
            'order_id' => $order->id,
            'type' => 'order_status_update',
            'title' => 'Test',
            'message' => 'Test message',
        ]);

        $response = $this->postJson("/api/customer/notifications/{$notification->id}/read");

        $response->assertStatus(200);

        $notification->refresh();
        $this->assertNotNull($notification->read_at);
    }

    public function test_customer_can_mark_all_notifications_as_read()
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '123456',
            'status' => 'active',
        ]);

        $address = $customer->addresses()->create([
            'name' => 'Home',
            'title' => 'Test',
            'address_details' => '123 Test St',
        ]);

        $service = Service::create(['name' => 'S', 'description' => 'D', 'active' => true]);
        $variant = ServiceVariant::create(['service_id' => $service->id, 'name' => 'V', 'description' => 'D']);

        $order1 = Order::create([
            'customer_id' => $customer->id,
            'customer_address_id' => $address->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $order2 = Order::create([
            'customer_id' => $customer->id,
            'customer_address_id' => $address->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'pending',
            'total_price' => 100,
        ]);

        $customer->notifications()->create([
            'order_id' => $order1->id,
            'type' => 'order_status_update',
            'title' => 'Test 1',
            'message' => 'Test message 1',
        ]);

        $customer->notifications()->create([
            'order_id' => $order2->id,
            'type' => 'order_status_update',
            'title' => 'Test 2',
            'message' => 'Test message 2',
        ]);

        $response = $this->postJson('/api/customer/notifications/read-all', [
            'customer_email' => 'customer@test.com',
        ]);

        $response->assertStatus(200);

        $this->assertEquals(0, $customer->notifications()->unread()->count());
    }
}

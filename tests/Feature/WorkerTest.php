<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Worker;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceVariant;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Timeslot;

class WorkerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_worker()
    {
        $response = $this->postJson('/api/admin/workers', [
            'name' => 'Worker One',
            'email' => 'worker@example.com',
            'phone' => '1234567890',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['email' => 'worker@example.com']);

        $this->assertDatabaseHas('workers', ['email' => 'worker@example.com']);
    }

    public function test_worker_can_login()
    {
        $worker = Worker::create([
            'name' => 'Worker One',
            'email' => 'worker@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/worker/login', [
            'email' => 'worker@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    public function test_admin_can_assign_order_to_worker()
    {
        // Setup data
        $worker = Worker::create([
            'name' => 'Worker One',
            'email' => 'worker@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $customer = Customer::create(['name' => 'Cust', 'email' => 'c@e.com', 'phone' => '123', 'status' => 'active']);
        $address = $customer->addresses()->create(['name' => 'H', 'title' => 'T', 'address_details' => 'A']);
        
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

        $response = $this->postJson("/api/admin/orders/{$order->id}/assign", [
            'worker_id' => $worker->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('order_worker', [
            'order_id' => $order->id,
            'worker_id' => $worker->id,
            'status' => 'assigned',
        ]);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'assigned',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'assigned',
            'changed_by_type' => 'system', // or admin as per controller logic
        ]);
    }

    public function test_worker_can_update_order_status()
    {
        // Setup data
        $worker = Worker::create([
            'name' => 'Worker One',
            'email' => 'worker@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $customer = Customer::create(['name' => 'Cust', 'email' => 'c@e.com', 'phone' => '123', 'status' => 'active']);
        $address = $customer->addresses()->create(['name' => 'H', 'title' => 'T', 'address_details' => 'A']);
        
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

        // Assign worker
        $order->workers()->attach($worker->id, ['status' => 'assigned']);

        // Login worker
        $token = $worker->createToken('test')->plainTextToken;

        // Update status to worker_on_way
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/worker/orders/{$order->id}/status", [
                'status' => 'worker_on_way',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'worker_on_way',
        ]);

        $this->assertDatabaseHas('order_worker', [
            'order_id' => $order->id,
            'worker_id' => $worker->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'worker_on_way',
            'changed_by_type' => 'worker',
            'changed_by_id' => $worker->id,
        ]);
    }
}

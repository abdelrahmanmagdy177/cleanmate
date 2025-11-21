<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Worker;
use App\Models\Timeslot;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer1 = Customer::where('email', 'alice@example.com')->first();
        $customer2 = Customer::where('email', 'bob@example.com')->first();
        
        $service = Service::where('name', 'Deep Cleaning')->first();
        $variant = $service->variants()->first();
        
        $worker1 = Worker::where('email', 'worker1@cleanmate.com')->first();
        $worker2 = Worker::where('email', 'worker2@cleanmate.com')->first();
        
        // Order 1: Completed order
        $order1 = Order::create([
            'customer_id' => $customer1->id,
            'customer_address_id' => $customer1->addresses()->where('is_default', true)->first()->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'completed',
            'total_price' => 120.00,
            'notes' => 'Please focus on kitchen and bathrooms',
        ]);
        
        $order1->workers()->attach($worker1->id, ['status' => 'completed', 'assigned_at' => now()->subDays(5)]);
        $order1->statusHistory()->create(['status' => 'pending', 'changed_by_type' => 'system']);
        $order1->statusHistory()->create(['status' => 'assigned', 'changed_by_type' => 'system']);
        $order1->statusHistory()->create(['status' => 'worker_on_way', 'changed_by_type' => 'worker', 'changed_by_id' => $worker1->id]);
        $order1->statusHistory()->create(['status' => 'in_progress', 'changed_by_type' => 'worker', 'changed_by_id' => $worker1->id]);
        $order1->statusHistory()->create(['status' => 'completed', 'changed_by_type' => 'worker', 'changed_by_id' => $worker1->id]);
        
        // Order 2: In progress
        $timeslot = Timeslot::where('day', now()->dayOfWeek)->first();
        
        $order2 = Order::create([
            'customer_id' => $customer2->id,
            'customer_address_id' => $customer2->addresses()->first()->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->format('Y-m-d'),
            'status' => 'in_progress',
            'total_price' => 75.00,
        ]);
        
        $order2->workers()->attach($worker2->id, ['status' => 'accepted', 'assigned_at' => now()->subHours(2)]);
        $order2->timeslots()->attach($timeslot->id, ['date' => now()->format('Y-m-d')]);
        $order2->statusHistory()->create(['status' => 'pending', 'changed_by_type' => 'system']);
        $order2->statusHistory()->create(['status' => 'assigned', 'changed_by_type' => 'system']);
        $order2->statusHistory()->create(['status' => 'worker_on_way', 'changed_by_type' => 'worker', 'changed_by_id' => $worker2->id]);
        $order2->statusHistory()->create(['status' => 'in_progress', 'changed_by_type' => 'worker', 'changed_by_id' => $worker2->id]);
        
        // Order 3: Pending assignment
        $order3 = Order::create([
            'customer_id' => $customer1->id,
            'customer_address_id' => $customer1->addresses()->where('name', 'Office')->first()->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'order_date' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'pending',
            'total_price' => 200.00,
            'notes' => 'Office space cleaning needed',
        ]);
        
        $order3->statusHistory()->create(['status' => 'pending', 'changed_by_type' => 'system']);
    }
}

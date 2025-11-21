<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Service;
use App\Models\ServiceVariant;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_timeslots()
    {
        $this->seed(\Database\Seeders\TimeslotSeeder::class);
        
        $date = now()->addDay();
        $dayOfWeek = $date->dayOfWeek;

        $response = $this->getJson('/api/customer/timeslots?date=' . $date->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
            
        // Verify that we get slots for the correct day
        // Since we seeded all days, we should get slots. 
        // Let's check if we can verify the day logic by ensuring we get slots.
        // A more robust test would be to seed only one day and request another, but the seeder seeds all.
        // We can check if the number of slots matches what we expect for one day (9 to 17 = 8 slots).
        $this->assertCount(8, $response->json('data'));
    }

    public function test_can_create_order()
    {
        $this->seed(\Database\Seeders\TimeslotSeeder::class);
        $this->seed(\Database\Seeders\ZoneSeeder::class);
        $this->seed(\Database\Seeders\AreaSeeder::class);
        
        $date = now()->addDay();
        $dayOfWeek = $date->dayOfWeek;
        
        $timeslot = \App\Models\Timeslot::where('day', $dayOfWeek)->first();

        $service = Service::create([
            'name' => 'Test Service',
            'description' => 'Test Description',
            'active' => true,
        ]);

        $variant = ServiceVariant::create([
            'service_id' => $service->id,
            'name' => 'Standard',
            'description' => 'Standard cleaning',
        ]);

        $variant->prices()->create([
            'min_space' => 0,
            'max_space' => 100,
            'price' => 100.00,
        ]);
        
        // Create customer and address first
        $customer = \App\Models\Customer::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'status' => 'active'
        ]);
        
        // Get an active area
        $area = \App\Models\Area::where('is_active', true)->first();
        
        $address = $customer->addresses()->create([
            'area_id' => $area->id,
            'name' => 'Home',
            'title' => 'My House',
            'address_details' => '123 Main St',
        ]);
        
        $response = $this->postJson('/api/customer/orders', [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'timeslot_id' => $timeslot->id,
            'customer_address_id' => $address->id,
            'space' => 50,
            'order_date' => $date->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Order created successfully']);

        // Verify order was created
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'variant_id' => $variant->id,
            'status' => 'pending',
        ]);

        // Verify price breakdown
        $responseData = $response->json();
        $this->assertArrayHasKey('price_breakdown', $responseData);
        
        $breakdown = $responseData['price_breakdown'];
        $this->assertEquals(100.00, $breakdown['service_price']);
        $this->assertEquals(14.00, $breakdown['vat_rate']);
        $this->assertEquals(14.00, $breakdown['vat_amount']); // 14% of 100
        $this->assertEquals(114.00, $breakdown['total_price']);

        // Verify timeslot was linked
        $this->assertDatabaseHas('timeslot_orders', [
            'timeslot_id' => $timeslot->id,
            'order_id' => $responseData['data']['id'],
            'date' => $date->format('Y-m-d'),
        ]);
    }
}

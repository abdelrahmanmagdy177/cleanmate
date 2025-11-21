<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Customer;

class CustomerAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_address()
    {
        $response = $this->postJson('/api/customer/customer-addresses', [
            'customer_email' => 'test@example.com',
            'name' => 'Home',
            'title' => 'My House',
            'address_details' => '123 Main St, City',
            'is_default' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Home']);

        $this->assertDatabaseHas('customers', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('customer_addresses', ['address_details' => '123 Main St, City']);
    }

    public function test_can_list_addresses()
    {
        $customer = Customer::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'status' => 'active'
        ]);

        $customer->addresses()->create([
            'name' => 'Work',
            'title' => 'Office',
            'address_details' => '456 Work St',
        ]);

        $response = $this->getJson('/api/customer/customer-addresses?customer_email=test@example.com');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Work']);
    }

    public function test_can_update_address()
    {
        $customer = Customer::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'status' => 'active'
        ]);

        $address = $customer->addresses()->create([
            'name' => 'Old Name',
            'title' => 'Old Title',
            'address_details' => 'Old Address',
        ]);

        $response = $this->putJson("/api/customer/customer-addresses/{$address->id}", [
            'name' => 'New Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'New Name']);
            
        $this->assertDatabaseHas('customer_addresses', ['id' => $address->id, 'name' => 'New Name']);
    }

    public function test_can_delete_address()
    {
        $customer = Customer::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'status' => 'active'
        ]);

        $address = $customer->addresses()->create([
            'name' => 'To Delete',
            'title' => 'Title',
            'address_details' => 'Address',
        ]);

        $response = $this->deleteJson("/api/customer/customer-addresses/{$address->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('customer_addresses', ['id' => $address->id]);
    }
}

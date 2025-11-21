<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Area;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some areas
        $downtown = Area::where('name', 'Downtown')->first();
        $cityCenter = Area::where('name', 'City Center')->first();
        $northSuburbs = Area::where('name', 'North Suburbs')->first();
        $farEast = Area::where('name', 'Far East')->first();

        $customer1 = Customer::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'phone' => '555-1001',
            'status' => 'active',
            'in_region' => true,
        ]);

        $customer1->addresses()->create([
            'area_id' => $downtown->id,
            'name' => 'Home',
            'title' => 'Main Residence',
            'address_details' => '123 Main Street, Apt 4B, Downtown',
            'is_default' => true,
        ]);

        $customer1->addresses()->create([
            'area_id' => $cityCenter->id,
            'name' => 'Office',
            'title' => 'Work Address',
            'address_details' => '456 Business Ave, Suite 200, City Center',
            'is_default' => false,
        ]);

        $customer2 = Customer::create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '555-1002',
            'status' => 'active',
            'in_region' => true,
        ]);

        $customer2->addresses()->create([
            'area_id' => $northSuburbs->id,
            'name' => 'Home',
            'title' => 'House',
            'address_details' => '789 Oak Street, North Suburbs',
            'is_default' => true,
        ]);

        $customer2->addresses()->create([
            'area_id' => $downtown->id,
            'name' => 'Vacation Home',
            'title' => 'Second Property',
            'address_details' => '100 Lake View, Downtown',
            'is_default' => false,
        ]);

        $customer3 = Customer::create([
            'name' => 'Carol White',
            'email' => 'carol@example.com',
            'phone' => '555-1003',
            'status' => 'active',
            'in_region' => false,
        ]);

        $customer3->addresses()->create([
            'area_id' => $farEast->id,
            'name' => 'Home',
            'title' => 'Apartment',
            'address_details' => '321 Pine Road, Far East',
            'is_default' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Area;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deep Cleaning Service
        $deepCleaning = Service::create([
            'name' => 'Deep Cleaning',
            'description' => 'Comprehensive deep cleaning service for your home',
            'active' => true,
        ]);

        $standardDeep = $deepCleaning->variants()->create([
            'name' => 'Standard Deep Clean',
            'description' => 'Regular deep cleaning package',
        ]);

        $standardDeep->prices()->createMany([
            ['min_space' => 0, 'max_space' => 50, 'price' => 75.00],
            ['min_space' => 51, 'max_space' => 100, 'price' => 120.00],
            ['min_space' => 101, 'max_space' => 200, 'price' => 200.00],
            ['min_space' => 201, 'max_space' => null, 'price' => 350.00],
        ]);

        $premiumDeep = $deepCleaning->variants()->create([
            'name' => 'Premium Deep Clean',
            'description' => 'Premium deep cleaning with eco-friendly products',
        ]);

        $premiumDeep->prices()->createMany([
            ['min_space' => 0, 'max_space' => 50, 'price' => 100.00],
            ['min_space' => 51, 'max_space' => 100, 'price' => 160.00],
            ['min_space' => 101, 'max_space' => 200, 'price' => 270.00],
            ['min_space' => 201, 'max_space' => null, 'price' => 450.00],
        ]);

        // Regular Cleaning Service
        $regularCleaning = Service::create([
            'name' => 'Regular Cleaning',
            'description' => 'Routine cleaning service for maintaining your space',
            'active' => true,
        ]);

        $basicRegular = $regularCleaning->variants()->create([
            'name' => 'Basic Clean',
            'description' => 'Essential cleaning tasks',
        ]);

        $basicRegular->prices()->createMany([
            ['min_space' => 0, 'max_space' => 50, 'price' => 50.00],
            ['min_space' => 51, 'max_space' => 100, 'price' => 80.00],
            ['min_space' => 101, 'max_space' => 200, 'price' => 130.00],
            ['min_space' => 201, 'max_space' => null, 'price' => 220.00],
        ]);

        // Move-In/Move-Out Cleaning
        $moveInOut = Service::create([
            'name' => 'Move-In/Move-Out Cleaning',
            'description' => 'Thorough cleaning for moving transitions',
            'active' => true,
        ]);

        $moveInOutVariant = $moveInOut->variants()->create([
            'name' => 'Complete Move Clean',
            'description' => 'Full cleaning for empty properties',
        ]);

        $moveInOutVariant->prices()->createMany([
            ['min_space' => 0, 'max_space' => 50, 'price' => 90.00],
            ['min_space' => 51, 'max_space' => 100, 'price' => 150.00],
            ['min_space' => 101, 'max_space' => 200, 'price' => 250.00],
            ['min_space' => 201, 'max_space' => null, 'price' => 400.00],
        ]);


        // Attach all services to all active areas for initial setup
        $areas = Area::where('is_active', true)->get();
        $services = Service::all();

        foreach ($services as $service) {
            $service->areas()->attach($areas);
        }
    }
}

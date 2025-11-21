<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Central Zone',
                'is_active' => true,
                'description' => 'Central city areas',
            ],
            [
                'name' => 'Suburban Zone',
                'is_active' => true,
                'description' => 'Suburban areas',
            ],
            [
                'name' => 'Outer Zone',
                'is_active' => true,
                'description' => 'Outer city areas',
            ],
            [
                'name' => 'Premium Zone',
                'is_active' => false,
                'description' => 'Premium areas (currently inactive)',
            ],
        ];

        foreach ($zones as $zone) {
            Zone::create($zone);
        }
    }
}

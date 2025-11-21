<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        // Get zones
        $centralZone = Zone::where('name', 'Central Zone')->first();
        $suburbanZone = Zone::where('name', 'Suburban Zone')->first();
        $outerZone = Zone::where('name', 'Outer Zone')->first();

        // Central Zone areas
        $centralAreas = [
            ['name' => 'Downtown', 'is_active' => true],
            ['name' => 'City Center', 'is_active' => true],
            ['name' => 'Business District', 'is_active' => true],
        ];

        foreach ($centralAreas as $area) {
            Area::create(array_merge($area, ['zone_id' => $centralZone->id]));
        }

        // Suburban Zone areas
        $suburbanAreas = [
            ['name' => 'North Suburbs', 'is_active' => true],
            ['name' => 'East Suburbs', 'is_active' => true],
            ['name' => 'West Suburbs', 'is_active' => true],
            ['name' => 'South Suburbs', 'is_active' => false], // Inactive area
        ];

        foreach ($suburbanAreas as $area) {
            Area::create(array_merge($area, ['zone_id' => $suburbanZone->id]));
        }

        // Outer Zone areas
        $outerAreas = [
            ['name' => 'Far North', 'is_active' => true],
            ['name' => 'Far East', 'is_active' => true],
            ['name' => 'Far West', 'is_active' => true],
            ['name' => 'Far South', 'is_active' => true],
        ];

        foreach ($outerAreas as $area) {
            Area::create(array_merge($area, ['zone_id' => $outerZone->id]));
        }
    }
}

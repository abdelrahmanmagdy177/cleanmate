<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timeslot;

class TimeslotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = 9;
        $end = 17;

        // Loop through days 0 (Sunday) to 6 (Saturday)
        for ($day = 0; $day <= 6; $day++) {
            for ($i = $start; $i < $end; $i++) {
                Timeslot::create([
                    'day' => $day,
                    'start_time' => sprintf('%02d:00:00', $i),
                    'end_time' => sprintf('%02d:00:00', $i + 1),
                    'capacity' => 2, // Default capacity
                    'is_active' => true,
                ]);
            }
        }
    }
}

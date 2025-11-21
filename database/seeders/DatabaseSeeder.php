<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@cleanmate.com',
        ]);

        // Seed all data
        $this->call([
            TimeslotSeeder::class,
            ServiceSeeder::class,
            ZoneSeeder::class,
            AreaSeeder::class,
            WorkerSeeder::class,
            CustomerSeeder::class,
            OrderSeeder::class,
        ]);
    }
}

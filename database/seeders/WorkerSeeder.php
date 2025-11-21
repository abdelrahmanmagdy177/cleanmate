<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Worker;
use Illuminate\Support\Facades\Hash;

class WorkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Worker::create([
            'name' => 'John Worker',
            'email' => 'worker1@cleanmate.com',
            'phone' => '555-0101',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Worker::create([
            'name' => 'Jane Worker',
            'email' => 'worker2@cleanmate.com',
            'phone' => '555-0102',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        Worker::create([
            'name' => 'Mike Worker',
            'email' => 'worker3@cleanmate.com',
            'phone' => '555-0103',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed admin and regular users
        $this->call([
            AdminSeeder::class,
        ]);

        // Create regular user
        User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'is_admin' => false,
        ]);

        // Seed categories and products
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            CouponSeeder::class,
        ]);
    }
}

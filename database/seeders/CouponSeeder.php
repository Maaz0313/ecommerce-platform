<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample coupons
        Coupon::create([
            'code' => 'WELCOME10',
            'description' => 'Welcome discount 10% off',
            'type' => 'percentage',
            'value' => 10,
            'min_order_amount' => 0,
            'max_uses' => 100,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(3),
        ]);

        Coupon::create([
            'code' => 'SAVE20',
            'description' => '20% off on orders over $100',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 100,
            'max_uses' => 50,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(1),
        ]);

        Coupon::create([
            'code' => 'FLAT25',
            'description' => 'Flat $25 off on orders over $150',
            'type' => 'fixed',
            'value' => 25,
            'min_order_amount' => 150,
            'max_uses' => 30,
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addMonths(2),
        ]);
    }
}

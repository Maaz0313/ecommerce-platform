<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class UpdateCouponDescriptionsSeeder extends Seeder
{
    /**
     * Run the database seeds to update coupon descriptions to reflect Pakistani Rupee.
     */
    public function run(): void
    {
        // Update coupon descriptions to reflect Pakistani Rupee
        $coupons = Coupon::all();

        foreach ($coupons as $coupon) {
            // Update description for SAVE20 coupon
            if ($coupon->code === 'SAVE20') {
                $coupon->description = '20% off on orders over ₨28,000';
                $coupon->save();
                $this->command->info("Updated description for {$coupon->code}: {$coupon->description}");
            }

            // Update description for FLAT25 coupon
            if ($coupon->code === 'FLAT25') {
                $coupon->description = 'Flat ₨7,000 off on orders over ₨42,000';
                $coupon->save();
                $this->command->info("Updated description for {$coupon->code}: {$coupon->description}");
            }
        }

        $this->command->info('All coupon descriptions have been updated to reflect Pakistani Rupee values.');
    }
}

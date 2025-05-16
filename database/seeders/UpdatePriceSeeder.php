<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Database\Seeder;

class UpdatePriceSeeder extends Seeder
{
    /**
     * Run the database seeds to update prices to Pakistani Rupee values.
     * Using an approximate conversion rate of 280 PKR to 1 USD.
     */
    public function run(): void
    {
        // Conversion rate from USD to PKR
        $conversionRate = 280;

        // Update product prices
        $products = Product::all();
        foreach ($products as $product) {
            // Convert price from USD to PKR
            $pkrPrice = round($product->price * $conversionRate, 0);

            // Update the product price
            $product->price = $pkrPrice;
            $product->save();

            $this->command->info("Updated price for {$product->name}: ₨{$pkrPrice}");
        }

        // Update fixed coupon values and minimum order amounts
        $coupons = Coupon::all();
        foreach ($coupons as $coupon) {
            // For fixed amount coupons, convert the value
            if ($coupon->type === 'fixed') {
                $pkrValue = round($coupon->value * $conversionRate, 0);
                $coupon->value = $pkrValue;
                $this->command->info("Updated fixed coupon {$coupon->code} value to: ₨{$pkrValue}");
            }

            // Convert minimum order amount for all coupons
            $pkrMinAmount = round($coupon->min_order_amount * $conversionRate, 0);
            $coupon->min_order_amount = $pkrMinAmount;

            $coupon->save();
            $this->command->info("Updated coupon {$coupon->code} minimum order amount to: ₨{$pkrMinAmount}");
        }

        $this->command->info('All prices have been updated to Pakistani Rupee values.');
    }
}

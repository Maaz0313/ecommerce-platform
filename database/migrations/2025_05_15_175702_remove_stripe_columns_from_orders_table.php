<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop Stripe-related columns if they exist
            if (Schema::hasColumn('orders', 'stripe_payment_intent_id')) {
                $table->dropColumn('stripe_payment_intent_id');
            }

            if (Schema::hasColumn('orders', 'stripe_payment_status')) {
                $table->dropColumn('stripe_payment_status');
            }

            // Update payment_method column comment to remove Stripe references
            if (Schema::hasColumn('orders', 'payment_method')) {
                // First, get the current column definition
                $connection = Schema::getConnection();
                $prefix = $connection->getTablePrefix();
                $table_name = $prefix . 'orders';

                // Remove the comment from the payment_method column
                DB::statement("ALTER TABLE {$table_name} MODIFY payment_method VARCHAR(255) DEFAULT 'cod'");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // This migration is not reversible
        });
    }
};

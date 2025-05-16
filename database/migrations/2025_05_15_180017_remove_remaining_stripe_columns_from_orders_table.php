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
            // Drop payment_status column if it exists
            if (Schema::hasColumn('orders', 'payment_status')) {
                $table->dropColumn('payment_status');
            }

            // Update payment_method column to remove any Stripe-related comments
            $connection = Schema::getConnection();
            $prefix = $connection->getTablePrefix();
            $table_name = $prefix . 'orders';

            // Modify payment_method column to remove any comments
            DB::statement("ALTER TABLE {$table_name} MODIFY payment_method VARCHAR(255) DEFAULT 'cod'");
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

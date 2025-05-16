<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing order statuses to Amazon style
        DB::table('orders')
            ->where('status', 'pending')
            ->update(['status' => 'order_received']);

        DB::table('orders')
            ->where('status', 'processing')
            ->update(['status' => 'preparing_for_shipment']);

        DB::table('orders')
            ->where('status', 'completed')
            ->update(['status' => 'delivered']);

        // Note: 'cancelled' status remains the same
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original status names
        DB::table('orders')
            ->where('status', 'order_received')
            ->update(['status' => 'pending']);

        DB::table('orders')
            ->where('status', 'preparing_for_shipment')
            ->update(['status' => 'processing']);

        DB::table('orders')
            ->where('status', 'shipped')
            ->update(['status' => 'processing']);

        DB::table('orders')
            ->where('status', 'delivered')
            ->update(['status' => 'completed']);

        // Note: 'cancelled' status remains the same
    }
};

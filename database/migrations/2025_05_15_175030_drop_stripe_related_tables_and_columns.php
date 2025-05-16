<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop Stripe-related tables
        Schema::dropIfExists('subscription_items');
        Schema::dropIfExists('subscriptions');

        // Drop Stripe-related columns from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'stripe_id')) {
                $table->dropIndex(['stripe_id']);
                $table->dropColumn([
                    'stripe_id',
                    'pm_type',
                    'pm_last_four',
                    'trial_ends_at',
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is irreversible
    }
};

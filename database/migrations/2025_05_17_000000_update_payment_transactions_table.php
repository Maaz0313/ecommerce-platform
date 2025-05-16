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
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('payment_method')->default('stripe');
            $table->string('transaction_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('PKR');
            $table->string('status');
            $table->text('metadata')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'order_id',
                'user_id',
                'payment_method',
                'transaction_id',
                'payment_intent_id',
                'amount',
                'currency',
                'status',
                'metadata'
            ]);
        });
    }
};

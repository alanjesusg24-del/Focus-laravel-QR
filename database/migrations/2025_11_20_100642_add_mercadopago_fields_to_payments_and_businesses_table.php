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
        // Add MercadoPago fields to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->string('mercadopago_preference_id')->nullable()->after('stripe_subscription_id');
            $table->string('mercadopago_payment_id')->nullable()->after('mercadopago_preference_id');
            $table->string('mercadopago_status')->nullable()->after('mercadopago_payment_id');
            $table->text('mercadopago_response')->nullable()->after('mercadopago_status');
            $table->string('payment_provider')->default('stripe')->after('mercadopago_response'); // 'stripe' or 'mercadopago'
        });

        // Add subscription fields to businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->timestamp('subscription_start_date')->nullable()->after('last_payment_date');
            $table->timestamp('subscription_end_date')->nullable()->after('subscription_start_date');
            $table->boolean('subscription_active')->default(false)->after('subscription_end_date');
            $table->integer('subscription_days')->default(30)->after('subscription_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'mercadopago_preference_id',
                'mercadopago_payment_id',
                'mercadopago_status',
                'mercadopago_response',
                'payment_provider'
            ]);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_start_date',
                'subscription_end_date',
                'subscription_active',
                'subscription_days'
            ]);
        });
    }
};

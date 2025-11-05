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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('plan_id');
            $table->decimal('amount', 10, 2);
            $table->string('stripe_payment_id', 255)->nullable();
            $table->string('stripe_subscription_id', 255)->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('payment_date')->useCurrent();
            $table->timestamp('next_payment_date')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign keys
            $table->foreign('business_id')
                  ->references('business_id')
                  ->on('businesses')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreign('plan_id')
                  ->references('plan_id')
                  ->on('plans')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Indexes
            $table->index('business_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('business_id');
            $table->string('folio_number', 100)->unique();
            $table->text('description')->nullable();
            $table->text('qr_code_url')->nullable()->comment('QR code URL for scanning');
            $table->string('qr_token', 100)->unique()->comment('Unique token for linking');
            $table->string('pickup_token', 100)->unique()->comment('Separate token for pickup');
            $table->enum('status', ['pending', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('mobile_user_id')->nullable()->comment('NULL until scanned');
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('business_id')
                  ->references('business_id')
                  ->on('businesses')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Indexes
            $table->index(['business_id', 'status']);
            $table->index('folio_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

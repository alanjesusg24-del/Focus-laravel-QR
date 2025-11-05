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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('mobile_user_id');
            $table->enum('type', ['order_ready', 'order_cancelled', 'reminder']);
            $table->string('title', 255);
            $table->text('message');
            $table->boolean('sent_successfully')->default(false);
            $table->timestamp('sent_at')->useCurrent();

            // Foreign keys
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('orders')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Indexes
            $table->index('order_id');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

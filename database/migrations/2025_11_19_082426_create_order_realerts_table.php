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
        Schema::create('order_realerts', function (Blueprint $table) {
            $table->id('realert_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('alert_number')->comment('Sequential number of this alert (1, 2, 3, etc.)');
            $table->timestamp('sent_at');
            $table->string('notification_type')->default('ready_reminder')->comment('Type of notification sent');
            $table->boolean('was_delivered')->default(true);
            $table->text('response_message')->nullable()->comment('FCM response or error message');
            $table->timestamps();

            // Foreign key
            $table->foreign('order_id')
                ->references('order_id')
                ->on('orders')
                ->onDelete('cascade');

            // Indexes
            $table->index(['order_id', 'sent_at']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_realerts');
    }
};

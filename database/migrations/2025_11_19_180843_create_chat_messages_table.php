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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->unsignedBigInteger('order_id');
            $table->enum('sender_type', ['business', 'customer'])->comment('business = negocio, customer = cliente movil');
            $table->unsignedBigInteger('sender_id')->comment('business_id o mobile_users.id dependiendo del sender_type');
            $table->text('message');
            $table->string('attachment_url')->nullable()->comment('URL de imagen o archivo adjunto');
            $table->boolean('is_read')->default(false)->comment('Si el mensaje ha sido leido por el receptor');
            $table->timestamp('read_at')->nullable()->comment('Cuando fue leido el mensaje');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');

            // Indexes para mejorar rendimiento
            $table->index(['order_id', 'created_at']);
            $table->index(['sender_type', 'sender_id']);
            $table->index('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};

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
        Schema::table('orders', function (Blueprint $table) {
            // Agregar campos necesarios para la app móvil
            $table->string('order_number')->nullable()->after('order_id'); // Número de orden legible
            $table->string('customer_name')->nullable()->after('business_id');
            $table->string('customer_phone')->nullable()->after('customer_name');
            $table->string('customer_email')->nullable()->after('customer_phone');
            $table->decimal('total_amount', 10, 2)->nullable()->after('description');
            $table->timestamp('associated_at')->nullable()->after('mobile_user_id'); // Cuando se escaneó el QR
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_number', 'customer_name', 'customer_phone', 'customer_email', 'total_amount', 'associated_at']);
            $table->dropSoftDeletes();
        });
    }
};

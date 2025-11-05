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
        // Update plans table to use modular pricing
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->default(0)->after('price')
                ->comment('Precio base mensual del sistema');
            $table->decimal('chat_module_price', 10, 2)->default(0)->after('base_price')
                ->comment('Precio adicional por módulo de chat');
            $table->decimal('retention_price_per_month', 10, 2)->default(0)->after('chat_module_price')
                ->comment('Precio por mes adicional de retención de datos');
        });

        // Update businesses table to track modules and retention
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('has_chat_module')->default(false)->after('theme')
                ->comment('Si el negocio tiene activado el módulo de chat');
            $table->integer('data_retention_months')->default(1)->after('has_chat_module')
                ->comment('Meses de retención de datos (1, 3, 6, 12)');
            $table->decimal('monthly_price', 10, 2)->nullable()->after('data_retention_months')
                ->comment('Precio mensual calculado según módulos activos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'chat_module_price', 'retention_price_per_month']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['has_chat_module', 'data_retention_months', 'monthly_price']);
        });
    }
};

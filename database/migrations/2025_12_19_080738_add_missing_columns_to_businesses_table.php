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
        Schema::table('businesses', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('businesses', 'has_chat_module')) {
                $table->boolean('has_chat_module')->default(false)->after('logo_url')
                    ->comment('Si el negocio tiene activado el módulo de chat');
            }
            if (!Schema::hasColumn('businesses', 'data_retention_months')) {
                $table->integer('data_retention_months')->default(1)->after('has_chat_module')
                    ->comment('Meses de retención de datos (1, 3, 6, 12)');
            }
            if (!Schema::hasColumn('businesses', 'monthly_price')) {
                $table->decimal('monthly_price', 10, 2)->nullable()->after('data_retention_months')
                    ->comment('Precio mensual calculado según módulos activos');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['has_chat_module', 'data_retention_months', 'monthly_price']);
        });
    }
};

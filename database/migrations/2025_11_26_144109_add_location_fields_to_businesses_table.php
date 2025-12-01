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
            // Información de dirección adicional
            $table->text('address_details')->nullable()->after('address')->comment('Detalles adicionales de la dirección');
            $table->string('city', 100)->nullable()->after('address_details')->comment('Ciudad del negocio');
            $table->string('state', 100)->nullable()->after('city')->comment('Estado/Provincia');
            $table->string('postal_code', 20)->nullable()->after('state')->comment('Código postal');

            // Control de visibilidad de ubicación
            $table->boolean('is_location_public')->default(true)->after('postal_code')->comment('Si la ubicación es visible para usuarios');

            // Índices para optimizar búsquedas geográficas
            $table->index(['latitude', 'longitude'], 'idx_location');
            $table->index(['city', 'state'], 'idx_city_state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_location');
            $table->dropIndex('idx_city_state');

            // Eliminar columnas
            $table->dropColumn([
                'address_details',
                'city',
                'state',
                'postal_code',
                'is_location_public',
            ]);
        });
    }
};

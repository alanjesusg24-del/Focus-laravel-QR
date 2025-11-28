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
        Schema::table('mobile_users', function (Blueprint $table) {
            // Agregar campos de autenticación
            $table->string('email')->unique()->nullable()->after('id');
            $table->string('password')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('password');

            // Modificar device_id para que no sea único (puede ser null si se registra primero sin dispositivo)
            $table->dropUnique(['device_id']);
            $table->string('device_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Eliminar campos agregados
            $table->dropColumn(['email', 'password', 'email_verified_at']);

            // Restaurar device_id como único y requerido
            $table->string('device_id')->unique()->nullable(false)->change();
        });
    }
};

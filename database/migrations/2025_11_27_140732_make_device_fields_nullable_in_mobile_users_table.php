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
            // Hacer que los campos de dispositivo sean opcionales
            $table->string('device_type')->nullable()->change();
            $table->string('device_model')->nullable()->change();
            $table->string('os_version')->nullable()->change();
            $table->string('app_version')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_users', function (Blueprint $table) {
            // Restaurar campos como requeridos
            $table->string('device_type')->nullable(false)->change();
            $table->string('device_model')->nullable()->change();
            $table->string('os_version')->nullable()->change();
            $table->string('app_version')->nullable()->change();
        });
    }
};

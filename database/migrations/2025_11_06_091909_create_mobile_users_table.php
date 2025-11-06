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
        Schema::create('mobile_users', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // UUID del dispositivo
            $table->string('fcm_token')->nullable(); // Token para notificaciones push
            $table->string('device_type'); // 'android' o 'ios'
            $table->string('device_model')->nullable(); // Ej: "Samsung Galaxy S21"
            $table->string('os_version')->nullable(); // Ej: "Android 15"
            $table->string('app_version')->nullable(); // Ej: "1.0.0"
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_users');
    }
};

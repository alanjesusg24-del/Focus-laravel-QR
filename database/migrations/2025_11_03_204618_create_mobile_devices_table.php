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
        Schema::create('mobile_devices', function (Blueprint $table) {
            $table->id('mobile_device_id');
            $table->unsignedBigInteger('mobile_user_id');
            $table->string('fcm_token', 500)->unique();
            $table->enum('platform', ['ios', 'android']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('mobile_user_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_devices');
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('profile_photo_url')->nullable()->after('google_id');
            $table->string('device_id')->nullable()->after('profile_photo_url');
            $table->string('password')->nullable()->change(); // Hacer password opcional para Google
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'profile_photo_url', 'device_id']);
        });
    }
};

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
        Schema::table('plans', function (Blueprint $table) {
            // Módulo de chat
            $table->boolean('has_chat_module')->default(false)->after('is_active')->comment('Enable/disable chat module for this plan');

            // Sistema de re-alertas
            $table->boolean('has_realerts')->default(false)->after('has_chat_module')->comment('Enable/disable re-alerts for ready orders');
            $table->integer('realert_interval_minutes')->nullable()->after('has_realerts')->comment('Minutes between re-alerts when order is ready');
            $table->integer('realert_max_count')->nullable()->after('realert_interval_minutes')->comment('Maximum number of re-alerts to send');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['has_chat_module', 'has_realerts', 'realert_interval_minutes', 'realert_max_count']);
        });
    }
};

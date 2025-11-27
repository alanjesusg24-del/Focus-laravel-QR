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
            $table->integer('realert_days')->nullable()->after('realert_max_count')->comment('Days component of realert interval');
            $table->integer('realert_hours')->nullable()->after('realert_days')->comment('Hours component of realert interval');
            $table->integer('realert_minutes')->nullable()->after('realert_hours')->comment('Minutes component of realert interval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['realert_days', 'realert_hours', 'realert_minutes']);
        });
    }
};

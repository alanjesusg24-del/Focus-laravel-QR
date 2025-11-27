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
            // Eliminar campos que ya no se usan (ahora todo se hereda del plan)
            if (Schema::hasColumn('businesses', 'monthly_price')) {
                $table->dropColumn('monthly_price');
            }
            if (Schema::hasColumn('businesses', 'data_retention_months')) {
                $table->dropColumn('data_retention_months');
            }
            if (Schema::hasColumn('businesses', 'has_chat_module')) {
                $table->dropColumn('has_chat_module');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->decimal('monthly_price', 10, 2)->nullable();
            $table->integer('data_retention_months')->default(1);
            $table->boolean('has_chat_module')->default(false);
        });
    }
};

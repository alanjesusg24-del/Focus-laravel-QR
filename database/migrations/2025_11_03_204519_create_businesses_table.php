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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id('business_id');
            $table->string('business_name', 255)->comment('Business legal name');
            $table->string('rfc', 13)->unique();
            $table->string('email', 255)->unique();
            $table->string('password_hash', 255);
            $table->string('phone', 10);
            $table->text('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedBigInteger('plan_id');
            $table->timestamp('registration_date')->useCurrent();
            $table->timestamp('last_payment_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('theme', 50)->default('professional')->comment('professional or food');
            $table->timestamps();

            // Foreign keys
            $table->foreign('plan_id')
                  ->references('plan_id')
                  ->on('plans')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // Indexes
            $table->index('rfc');
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};

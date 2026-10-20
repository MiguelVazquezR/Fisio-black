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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('patient_phone', 32);
            $table->string('patient_email')->nullable();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->boolean('is_home_service')->default(false);
            $table->text('address_notes')->nullable();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['appointment_date', 'appointment_time']);
            $table->index(['appointment_date', 'status']);
            $table->index('patient_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

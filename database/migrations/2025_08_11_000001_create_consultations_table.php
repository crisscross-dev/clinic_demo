<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('patient_id')
                ->constrained('patient_infos')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('assessed_by')->nullable();

            // Consultation details
            $table->string('chief_complaint')->nullable();
            $table->decimal('temperature', 4, 1)->nullable(); // e.g. 37.5
            $table->string('blood_pressure', 20)->nullable(); // e.g. 120/80
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('spo2')->nullable(); // oxygen saturation in %
            $table->date('lmp')->nullable(); // last menstrual period
            $table->string('pain_scale', 50)->nullable();

            // Notes
            $table->text('assessment')->nullable();
            $table->text('intervention')->nullable();
            $table->string('outcome', 100)->nullable();
            $table->json('dispensed_medicines')->nullable();

            // Timestamps (Laravel handles these instead of DB default)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};

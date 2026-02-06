<?php
// php artisan migrate --path=database/migrations/2025_08_08_151605_create_patient_infos_table.php
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
        Schema::create('patient_infos', function (Blueprint $table) {
            $table->id();

            // Basic identity
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix', 10)->nullable();


            // Demographics
            $table->string('sex', 10)->nullable(); // e.g., Male/Female
            $table->integer('age')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('nationality', 64)->nullable();
            $table->string('religion', 64)->nullable();

            // Contact
            $table->string('contact_no', 32)->nullable();
            $table->string('address')->nullable();

            // School-related (required for students)
            $table->string('department', 64)->nullable();
            $table->string('course') ->nullable();
            $table->string('year_level', 16)->nullable();

            // Emergency / guardian
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('father_contact_no', 32)->nullable();
            $table->string('mother_contact_no', 32)->nullable();
            $table->string('guardian_contact_no', 32)->nullable();
            $table->string('guardian_address', 255)->nullable();

            // Medical info
            $table->text('allergies')->nullable();
            $table->string('other_allergies')->nullable();
            $table->text('treatments')->nullable();
            $table->text('covid')->nullable();
            $table->string('flu_vaccine')->nullable();
            $table->string('other_vaccine')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('medication')->nullable();
            $table->text('lasthospitalization')->nullable();
            $table->text('consent')->nullable();
            $table->boolean('consent_form')->default(false)->comment('Admin-controlled lock for consent section: true = locked, false = unlocked');
            $table->boolean('consent_access_requested')->default(false)->comment('Student has requested access to edit consent: true = requested, false = not requested');
            $table->string('consent_by')->nullable();

            $table->string('signature')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks temporarily to allow dropping the table
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('patient_infos');
        Schema::enableForeignKeyConstraints();
    }
};

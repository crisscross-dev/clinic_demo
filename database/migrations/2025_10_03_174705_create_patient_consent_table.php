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
        Schema::create('patient_consent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_info_id');
            $table->text('consent_reason')->comment('Reason why student is requesting consent access');
            $table->timestamps();

            // Foreign key constraint with cascade delete
            $table->foreign('patient_info_id')
                ->references('id')
                ->on('patient_infos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Index for faster queries
            $table->index('patient_info_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_consent');
    }
};

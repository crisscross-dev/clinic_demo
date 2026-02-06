<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add student_account_id to patient_infos, consultations and patient_uploads tables
     */
    public function up(): void
    {
        // Add to patient_infos table
        Schema::table('patient_infos', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_infos', 'student_account_id')) {
                $table->foreignId('student_account_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('student_accounts')
                    ->cascadeOnDelete();
            }
        });

        // Add unique constraint on student_account_id
        Schema::table('patient_infos', function (Blueprint $table) {
            if (Schema::hasColumn('patient_infos', 'student_account_id')) {
                // Try to add unique constraint (will fail silently if it already exists)
                try {
                    $table->unique('student_account_id', 'unique_student_patient');
                } catch (\Exception $e) {
                    // Unique constraint already exists, continue
                }
            }
        });

        // Add to consultations table
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'student_account_id')) {
                $table->foreignId('student_account_id')
                    ->nullable()
                    ->after('admin_id')
                    ->constrained('student_accounts')
                    ->cascadeOnDelete();
            }
        });

        // Add to patient_uploads table
        Schema::table('patient_uploads', function (Blueprint $table) {
            if (!Schema::hasColumn('patient_uploads', 'student_account_id')) {
                $table->foreignId('student_account_id')
                    ->nullable()
                    ->after('patient_id')
                    ->constrained('student_accounts')
                    ->cascadeOnDelete();
            }

            // Also add original_name and file_size while we're here
            if (!Schema::hasColumn('patient_uploads', 'original_name')) {
                $table->string('original_name')->nullable()->after('file_path');
            }

            if (!Schema::hasColumn('patient_uploads', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('original_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_infos', function (Blueprint $table) {
            // Drop foreign key first (before unique constraint)
            if (Schema::hasColumn('patient_infos', 'student_account_id')) {
                try {
                    $table->dropForeign(['student_account_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
            }
        });

        // Drop unique constraint in a separate schema call
        Schema::table('patient_infos', function (Blueprint $table) {
            if (Schema::hasColumn('patient_infos', 'student_account_id')) {
                try {
                    $table->dropUnique('unique_student_patient');
                } catch (\Exception $e) {
                    // Unique constraint doesn't exist, continue
                }
            }
        });

        // Now drop the column
        Schema::table('patient_infos', function (Blueprint $table) {
            if (Schema::hasColumn('patient_infos', 'student_account_id')) {
                $table->dropColumn('student_account_id');
            }
        });

        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'student_account_id')) {
                try {
                    $table->dropForeign(['student_account_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                $table->dropColumn('student_account_id');
            }
        });

        Schema::table('patient_uploads', function (Blueprint $table) {
            if (Schema::hasColumn('patient_uploads', 'student_account_id')) {
                try {
                    $table->dropForeign(['student_account_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                $table->dropColumn('student_account_id');
            }

            if (Schema::hasColumn('patient_uploads', 'original_name')) {
                $table->dropColumn('original_name');
            }

            if (Schema::hasColumn('patient_uploads', 'file_size')) {
                $table->dropColumn('file_size');
            }
        });
    }
};

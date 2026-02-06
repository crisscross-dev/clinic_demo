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
        Schema::table('patient_infos', function (Blueprint $table) {
            // Add signature field only
            if (!Schema::hasColumn('patient_infos', 'signature')) {
                $table->string('signature')->nullable()->after('consent_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_infos', function (Blueprint $table) {
            // Drop signature field
            if (Schema::hasColumn('patient_infos', 'signature')) {
                $table->dropColumn('signature');
            }
        });
    }
};

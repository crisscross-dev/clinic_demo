<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: MySQL supports enum natively; if you're using another DB you may need to adjust.
     *
     * @return void
     */
    public function up()
    {
        // Add enum status column after consent_reason using raw SQL (MySQL ENUM)
        if (!Schema::hasColumn('patient_consent', 'status')) {
            DB::statement("ALTER TABLE `patient_consent` ADD COLUMN `status` ENUM('pending','granted','declined') NOT NULL DEFAULT 'pending' AFTER `consent_reason`;");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_consent', function (Blueprint $table) {
            if (Schema::hasColumn('patient_consent', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

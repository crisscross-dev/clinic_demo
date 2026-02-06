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
        Schema::table('admin', function (Blueprint $table) {
            // Add prefix field (Dr., Mr., etc.)
            if (!Schema::hasColumn('admin', 'prefix')) {
                $table->string('prefix', 20)->nullable()->after('id');
            }

            // Add email field
            if (!Schema::hasColumn('admin', 'email')) {
                $table->string('email')->nullable()->after('username');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (Schema::hasColumn('admin', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('admin', 'prefix')) {
                $table->dropColumn('prefix');
            }
        });
    }
};

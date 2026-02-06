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
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Add consultation_id field to track which consultation the transaction belongs to
            $table->unsignedBigInteger('consultation_id')->nullable()->after('admin_id');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');

            // Add notes field for additional information about the transaction
            $table->text('notes')->nullable()->after('consultation_id');

            // Update the type enum to include 'dispensed' for medicine dispensing
            $table->enum('type', ['restock', 'deduct', 'lost', 'expired', 'dispensed'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Check if columns exist before trying to drop them
            if (Schema::hasColumn('stock_transactions', 'consultation_id')) {
                // Try to drop foreign key first (it might not exist)
                try {
                    $table->dropForeign(['consultation_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                $table->dropColumn('consultation_id');
            }

            if (Schema::hasColumn('stock_transactions', 'notes')) {
                $table->dropColumn('notes');
            }

            // Keep 'dispensed' in the enum to avoid data truncation during rollback
            // Note: This preserves existing data integrity
            // $table->enum('type', ['restock', 'deduct', 'lost', 'expired'])->change();
        });
    }
};

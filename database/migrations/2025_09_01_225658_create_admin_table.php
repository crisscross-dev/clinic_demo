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
        // Check if table doesn't exist before creating it
        if (!Schema::hasTable('admin')) {
            Schema::create('admin', function (Blueprint $table) {
                $table->id();

                $table->string('username')->unique();
                $table->string('password');

                $table->string('lastname');
                $table->string('firstname');
                $table->string('middlename')->nullable();

                $table->string('role');

                $table->timestamps(); // created_at, updated_at
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks temporarily to allow dropping the table
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('admin');
        Schema::enableForeignKeyConstraints();
    }
};

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
        Schema::create('student_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable(); // 🕒 Last login timestamp
            $table->enum('status', ['active', 'inactive'])->default('active'); // ✅ Status field
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
        Schema::dropIfExists('student_accounts');
        Schema::enableForeignKeyConstraints();
    }
};

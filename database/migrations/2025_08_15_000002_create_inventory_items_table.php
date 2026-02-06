<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('total_stock')->default(0);
            $table->integer('low_stock_reminder')->default(5);
            $table->enum('status', ['In Stock', 'Low Stock', 'Out of Stock'])->default('In Stock');
            $table->timestamps();
        });
    }

    public function down()
    {
        // Disable foreign key checks temporarily to allow dropping the table
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('inventory_items');
        Schema::enableForeignKeyConstraints();
    }
};

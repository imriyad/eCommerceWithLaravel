<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();                        // Primary key
        $table->string('name');             // Product name
        $table->text('description')->nullable(); // Optional description
        $table->decimal('price', 10, 2);    // Product price
        $table->string('brand')->nullable(); // Optional brand
        $table->integer('stock')->default(0); // Stock quantity
        $table->string('sku')->unique()->nullable(); // Unique product code
        $table->boolean('is_active')->default(true); // Availability
        $table->string('image')->nullable(); // Image file path

        // Foreign key to categories table
        $table->unsignedBigInteger('category_id');
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

        $table->timestamps(); // created_at and updated_at
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

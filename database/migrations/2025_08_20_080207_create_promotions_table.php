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
       Schema::create('promotions', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Promotion name
    $table->enum('type', ['discount', 'bogo', 'free_shipping']); 
    $table->decimal('value', 10, 2)->nullable(); // e.g., 20% or $10 off
    $table->dateTime('start_date');
    $table->dateTime('end_date');
    $table->text('applicable_products')->nullable(); // JSON array of product IDs
    $table->boolean('status')->default(true); // Active/Inactive
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};

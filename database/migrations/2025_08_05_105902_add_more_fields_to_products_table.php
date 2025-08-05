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
    Schema::table('products', function (Blueprint $table) {
        $table->string('brand')->nullable();
        $table->integer('stock')->default(0);
        $table->string('sku')->unique()->nullable();
        $table->boolean('is_active')->default(true);
        $table->string('image')->nullable();
        $table->unsignedBigInteger('category_id')->nullable();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['category_id']);
        $table->dropColumn(['brand', 'stock', 'sku', 'is_active', 'image', 'category_id']);
    });
}

};

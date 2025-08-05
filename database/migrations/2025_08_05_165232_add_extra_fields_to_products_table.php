<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            $table->decimal('tax', 5, 2)->nullable()->after('discount_price');
            $table->string('weight')->nullable()->after('tax');
            $table->string('dimensions')->nullable()->after('weight');
            $table->string('tags')->nullable()->after('dimensions');
            $table->string('warranty')->nullable()->after('tags');
            $table->text('specifications')->nullable()->after('warranty');
            $table->string('color')->nullable()->after('specifications');
            $table->string('size')->nullable()->after('color');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('size');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'discount_price',
                'tax',
                'weight',
                'dimensions',
                'tags',
                'warranty',
                'specifications',
                'color',
                'size',
                'status',
            ]);
        });
    }
}

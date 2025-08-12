<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');  // foreign key to users table
            $table->string('order_number')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'declined', 'cancelled'])->default('pending');
            $table->decimal('grand_total', 10, 2);
            $table->integer('item_count');
            $table->enum('payment_method', ['cash_on_delivery', 'paypal', 'card']);
            $table->string('payment_status')->default('pending');
            $table->string('name');
            $table->string('email');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code', 20);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

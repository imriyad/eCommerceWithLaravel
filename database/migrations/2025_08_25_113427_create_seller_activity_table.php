<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id'); // reference users table
            $table->string('message');               // activity description
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')
                  ->on('users')   // change from sellers -> users
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_activity');
    }
};

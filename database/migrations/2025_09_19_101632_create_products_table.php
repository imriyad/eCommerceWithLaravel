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
    // Table already exists in database, so do nothing.
}

public function down(): void
{
    // Optionally, drop table if you want rollback to work:
    // Schema::dropIfExists('products');
}

};

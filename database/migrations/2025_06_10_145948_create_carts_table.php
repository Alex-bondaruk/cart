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
         Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('total_quantity')->default(0);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};

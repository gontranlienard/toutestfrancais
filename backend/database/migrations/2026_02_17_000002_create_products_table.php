<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('brand')->nullable();
            $table->string('image')->nullable();

            $table->foreignId('category_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();

            $table->index('brand');
            $table->index('category_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

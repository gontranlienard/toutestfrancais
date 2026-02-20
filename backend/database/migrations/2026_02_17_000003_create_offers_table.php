<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('site_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->decimal('price', 10, 2);
            $table->text('url');

            $table->timestamps();

            $table->unique(['product_id', 'site_id']);

            $table->index('price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('price_when_added', 10, 2)
                ->nullable();

            $table->timestamp('alert_sent_at')
                ->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'product_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
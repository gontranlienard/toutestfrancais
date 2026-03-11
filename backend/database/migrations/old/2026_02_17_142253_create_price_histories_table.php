<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('offer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();

            $table->timestamps();

            $table->index(['offer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};


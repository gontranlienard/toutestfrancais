<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unmapped_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('raw_category');
            $table->integer('occurrences')->default(1);
            $table->timestamps();

            $table->unique(['site_id', 'raw_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unmapped_categories');
    }
};


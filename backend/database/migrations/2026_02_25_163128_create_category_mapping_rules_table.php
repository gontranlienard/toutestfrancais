<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_mapping_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->nullable() // null = règle globale tous sites
                ->constrained()
                ->nullOnDelete();

            $table->string('keyword'); // mot-clé détecté

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('priority')->default(0); // priorité si conflit

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_mapping_rules');
    }
};

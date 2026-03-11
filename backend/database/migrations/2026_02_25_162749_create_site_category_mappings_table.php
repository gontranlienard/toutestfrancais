<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_category_mappings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            // ID ou slug de la catégorie côté site
            $table->string('site_category_identifier');

            // Nom brut récupéré côté site (optionnel mais utile debug)
            $table->string('site_category_name')->nullable();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['site_id', 'site_category_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_category_mappings');
    }
};


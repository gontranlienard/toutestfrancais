<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('site_id');
            $table->string('name');
            $table->string('slug');
            $table->string('url')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->timestamps();

            $table->index(['site_id', 'slug']);

            $table->foreign('site_id')
                ->references('id')
                ->on('sites')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')
                ->on('site_categories')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_categories');
    }
};

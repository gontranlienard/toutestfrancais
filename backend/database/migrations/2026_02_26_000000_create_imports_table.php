<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('site_slug');
            $table->string('filename');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])
                  ->default('pending');
            $table->integer('total_products')->default(0);
            $table->integer('processed_products')->default(0);
            $table->integer('success_products')->default(0);
            $table->integer('failed_products')->default(0);
            $table->longText('errors')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};

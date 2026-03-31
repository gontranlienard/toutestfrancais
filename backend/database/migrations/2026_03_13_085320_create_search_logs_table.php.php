<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('search_logs', function (Blueprint $table) {

            $table->id();

            $table->string('query');

            $table->integer('results_count')->nullable();

            $table->ipAddress('ip')->nullable();

            $table->string('session_id')->nullable();

            $table->timestamps();

            $table->index('query');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
    }
};
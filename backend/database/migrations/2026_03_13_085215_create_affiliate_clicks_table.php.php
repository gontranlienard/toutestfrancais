<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_clicks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('offer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('clicked_url');

            $table->ipAddress('ip')->nullable();

            $table->text('user_agent')->nullable();

            $table->string('referer')->nullable();

            $table->string('session_id')->nullable();

            $table->timestamps();

            $table->index('product_id');
            $table->index('offer_id');
            $table->index('site_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_clicks');
    }
};
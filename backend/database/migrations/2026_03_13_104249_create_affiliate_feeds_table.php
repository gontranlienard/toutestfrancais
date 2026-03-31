<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_feeds', function (Blueprint $table) {

            $table->id();

            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name'); // speedway, maxxess...

            $table->text('url'); // URL du feed effinity

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_feeds');
    }
};
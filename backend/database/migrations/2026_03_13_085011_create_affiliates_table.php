<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliates', function (Blueprint $table) {

            $table->id();

            $table->foreignId('site_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('platform')->nullable();

            $table->text('url_template');

            $table->decimal('commission_percent',5,2)->nullable();

            $table->integer('cookie_days')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
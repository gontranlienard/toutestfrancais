<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->unsignedBigInteger('site_category_id')
                  ->nullable()
                  ->after('site_id');

            $table->foreign('site_category_id')
                  ->references('id')
                  ->on('site_categories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['site_category_id']);
            $table->dropColumn('site_category_id');
        });
    }
};

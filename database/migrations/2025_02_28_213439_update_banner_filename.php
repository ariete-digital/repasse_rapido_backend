<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn('src');
            $table->string('filename');
            $table->string('original_filename')->nullable();
            $table->string('cdn_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner', function (Blueprint $table) {
            $table->dropColumn('filename');
            $table->dropColumn('original_filename');
            $table->dropColumn('cdn_url');
            $table->string('src');
        });
    }
};

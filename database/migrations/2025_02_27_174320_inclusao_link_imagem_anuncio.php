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
        Schema::table('imagens_anuncio', function (Blueprint $table) {
            $table->string('link')->nullable();
        });
        Schema::table('imagens_rascunho', function (Blueprint $table) {
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imagens_anuncio', function (Blueprint $table) {
            $table->dropColumn('link');
        });
        Schema::table('imagens_rascunho', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};

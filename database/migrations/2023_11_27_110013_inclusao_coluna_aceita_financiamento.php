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
        Schema::table('anuncios_rascunho', function (Blueprint $table) {
            $table->string('aceita_financiamento')->nullable();
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->string('aceita_financiamento')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios_rascunho', function (Blueprint $table) {
            $table->dropColumn('aceita_financiamento');
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn('aceita_financiamento');
        });
    }
};

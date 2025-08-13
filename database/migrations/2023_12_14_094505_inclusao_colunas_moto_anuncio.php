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
            $table->string('tipo_motor')->nullable();
            $table->string('refrigeracao')->nullable();
            $table->string('cilindrada')->nullable();
            $table->string('partida')->nullable();
            $table->string('freios')->nullable();
            $table->string('tipo_freio')->nullable();
            $table->string('alarme')->nullable();
            $table->string('alimentacao')->nullable();
            $table->string('controle_estabilidade')->nullable();
            $table->string('roda_liga')->nullable();
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->string('tipo_motor')->nullable();
            $table->string('refrigeracao')->nullable();
            $table->string('cilindrada')->nullable();
            $table->string('partida')->nullable();
            $table->string('freios')->nullable();
            $table->string('tipo_freio')->nullable();
            $table->string('alarme')->nullable();
            $table->string('alimentacao')->nullable();
            $table->string('controle_estabilidade')->nullable();
            $table->string('roda_liga')->nullable();
            $table->string('num_portas')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios_rascunho', function (Blueprint $table) {
            $table->dropColumn('tipo_motor')->nullable();
            $table->dropColumn('refrigeracao')->nullable();
            $table->dropColumn('cilindrada')->nullable();
            $table->dropColumn('partida')->nullable();
            $table->dropColumn('freios')->nullable();
            $table->dropColumn('tipo_freio')->nullable();
            $table->dropColumn('alarme')->nullable();
            $table->dropColumn('alimentacao')->nullable();
            $table->dropColumn('controle_estabilidade')->nullable();
            $table->dropColumn('roda_liga')->nullable();
        });

        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn('tipo_motor')->nullable();
            $table->dropColumn('refrigeracao')->nullable();
            $table->dropColumn('cilindrada')->nullable();
            $table->dropColumn('partida')->nullable();
            $table->dropColumn('freios')->nullable();
            $table->dropColumn('tipo_freio')->nullable();
            $table->dropColumn('alarme')->nullable();
            $table->dropColumn('alimentacao')->nullable();
            $table->dropColumn('controle_estabilidade')->nullable();
            $table->dropColumn('roda_liga')->nullable();
            $table->string('num_portas')->nullable(false)->change();
        });
    }
};

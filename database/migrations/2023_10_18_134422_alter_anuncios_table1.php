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
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn('luz_injecao_airbag');
            $table->boolean('luz_injecao')->nullable();
            $table->boolean('luz_airbag')->nullable();
            $table->boolean('luz_abs')->nullable();

            $table->dropColumn('pequena_monta');
            $table->dropColumn('media_monta');
            $table->dropColumn('grande_monta');
            $table->string('tipo_monta', 1)->nullable();

            $table->dropColumn('aceita_troca');
            $table->string('tipo_troca', 1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn('luz_injecao');
            $table->dropColumn('luz_airbag');
            $table->dropColumn('luz_abs');
            $table->boolean('luz_injecao_airbag')->nullable();

            $table->dropColumn('tipo_monta');
            $table->boolean('pequena_monta')->nullable();
            $table->boolean('media_monta')->nullable();
            $table->boolean('grande_monta')->nullable();

            $table->dropColumn('tipo_troca');
            $table->boolean('aceita_troca')->nullable();
        });
    }
};

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
        Schema::table('carrinho_compras', function (Blueprint $table) {
            $table->text('info_extra')->nullable();
            $table->unsignedBigInteger('id_anuncio')->nullable();
            $table->foreign('id_anuncio')
                ->references('id')
                ->on('anuncios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrinho_compras', function (Blueprint $table) {
            $table->dropForeign('carrinho_compras_id_anuncio_foreign');
            $table->dropColumn('id_anuncio');
            $table->dropColumn('info_extra');
        });
    }
};

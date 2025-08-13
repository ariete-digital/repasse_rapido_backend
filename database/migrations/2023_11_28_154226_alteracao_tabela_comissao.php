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
        Schema::table('comissao_usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pedido')->nullable()->change();
            $table->unsignedBigInteger('id_anuncio')->nullable();
            $table->foreign('id_anuncio')
                ->references('id')
                ->on('anuncios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comissao_usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pedido')->nullable(false)->change();
            $table->dropForeign('comissao_usuarios_id_anuncio_foreign');
            $table->dropColumn('id_anuncio');
        });
    }
};

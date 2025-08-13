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
        Schema::table('aceite_termo_clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_anuncio');
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
        Schema::table('aceite_termo_clientes', function (Blueprint $table) {
            $table->dropForeign(['id_anuncio']);
            $table->dropColumn('id_anuncio');
        });
    }
};

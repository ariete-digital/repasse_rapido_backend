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
        Schema::create('opcionais_rascunho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_opcional');
            $table->unsignedBigInteger('id_anuncio_rascunho');
            $table->timestamps();
            $table->foreign('id_opcional')
                ->references('id')
                ->on('opcionais')
                ->onDelete('cascade');
            $table->foreign('id_anuncio_rascunho')
                ->references('id')
                ->on('anuncios_rascunho')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opcionais_rascunho');
    }
};

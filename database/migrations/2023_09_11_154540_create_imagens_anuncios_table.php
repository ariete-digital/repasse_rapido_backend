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
        Schema::create('imagens_anuncio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anuncio');
            $table->string('arquivo');
            $table->timestamps();
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
        Schema::dropIfExists('imagens_anuncio');
    }
};

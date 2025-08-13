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
        Schema::create('opcionais_anuncio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_opcional');
            $table->unsignedBigInteger('id_anuncio');
            $table->timestamps();
            $table->foreign('id_opcional')
                ->references('id')
                ->on('opcionais')
                ->onDelete('cascade');
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
        Schema::dropIfExists('opcionais_anuncio');
    }
};

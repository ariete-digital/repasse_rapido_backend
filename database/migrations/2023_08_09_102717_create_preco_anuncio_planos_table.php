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
        Schema::create('precos_anuncios_planos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plano');
            $table->integer('quant_anuncios');
            $table->decimal('preco');
            $table->timestamps();
            $table->foreign('id_plano')
                ->references('id')
                ->on('planos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precos_anuncios_planos');
    }
};

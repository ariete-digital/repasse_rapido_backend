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
        Schema::create('aceite_termo_clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->boolean('aceite_termos_condicoes');
            $table->boolean('aceite_anuncio_fechado');
            $table->timestamps();
            $table->foreign('id_cliente')
                ->references('id')
                ->on('clientes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aceite_termo_clientes');
    }
};

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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_anuncio')->nullable();
            $table->string('nome_plano')->nullable();
            $table->string('tipo_plano', 1)->nullable();
            $table->string('quant_anuncios')->nullable();
            $table->string('marca_modelo')->nullable();
            $table->string('versao_veiculo')->nullable();
            $table->string('nome_proprietario')->nullable();
            $table->string('localizacao_proprietario')->nullable();
            $table->string('telefone_proprietario')->nullable();
            $table->string('celular_proprietario')->nullable();
            $table->timestamps();
            $table->foreign('id_cliente')
                ->references('id')
                ->on('clientes');
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
        Schema::dropIfExists('pedidos');
    }
};

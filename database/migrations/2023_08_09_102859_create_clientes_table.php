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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_cidade')->nullable();
            $table->string('num_documento')->nullable();
            $table->string('tipo', 2)->nullable(); //PF, PJ ou A
            $table->date('data_nasc')->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('cep')->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero')->nullable();
            $table->string('bairro')->nullable();
            $table->string('complemento')->nullable();
            $table->string('imagem_cnh')->nullable();
            $table->string('imagem_comprovante')->nullable();
            $table->string('nome_fantasia')->nullable();
            $table->string('cpf_responsavel')->nullable();
            $table->string('nome_responsavel')->nullable();
            $table->timestamps();
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('id_cidade')
                ->references('id')
                ->on('cidades')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

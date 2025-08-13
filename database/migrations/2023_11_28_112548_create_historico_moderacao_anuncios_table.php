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
        Schema::create('historico_moderacao_anuncios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anuncio')->nullable();
            $table->string('codigo_anuncio')->index();
            $table->string('email_usuario_moderacao')->index();
            $table->string('nome_usuario_moderacao');
            $table->string('perfil_usuario_moderacao');
            $table->string('cnh_anunciante');
            $table->string('comprovante_anunciante');
            $table->string('doc_complementar_anunciante')->nullable();
            $table->string('obs_moderacao');
            $table->timestamps();
            $table->foreign('id_anuncio')
                ->references('id')
                ->on('anuncios')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_moderacao_anuncios');
    }
};

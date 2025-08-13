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
        Schema::create('integracao_loja_conectada', function (Blueprint $table) {
            $table->id();
            $table->string('status'); // pendente, em_progresso, concluido, erro
            $table->text('resultado')->nullable(); // logs ou mensagem final
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integracao_loja_conectadas');
    }
};

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
        Schema::create('subregioes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->unsignedBigInteger('id_escritorio_regional');
            $table->string('nome');
            $table->decimal('percentual_comissao');
            $table->timestamps();
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('id_escritorio_regional')
                ->references('id')
                ->on('escritorios_regionais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subregioes');
    }
};

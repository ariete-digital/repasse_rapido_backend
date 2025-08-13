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
        Schema::create('localidades_subregioes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_uf')->nullable();
            $table->unsignedBigInteger('id_subregiao');
            $table->integer('cep_inicial')->nullable();
            $table->integer('cep_final')->nullable();
            $table->timestamps();
            $table->foreign('id_uf')
                ->references('id')
                ->on('ufs');
            $table->foreign('id_subregiao')
                ->references('id')
                ->on('subregioes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('localidades_subregioes');
    }
};

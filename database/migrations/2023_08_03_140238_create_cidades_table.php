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
        Schema::create('cidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ibge');
            $table->unsignedBigInteger('id_uf');
            $table->string('nome');
            $table->timestamps();
            $table->foreign('id_uf')
                ->references('id')
                ->on('ufs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cidades');
    }
};

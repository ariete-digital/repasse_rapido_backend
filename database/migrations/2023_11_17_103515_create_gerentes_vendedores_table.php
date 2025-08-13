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
        Schema::create('gerentes_vendedores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_gerente');
            $table->unsignedBigInteger('id_vendedor');
            $table->timestamps();
            $table->foreign('id_gerente')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('id_vendedor')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gerentes_vendedores');
    }
};

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
        Schema::create('comissao_usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_pedido');
            $table->decimal('percentual', 8, 2);
            $table->decimal('valor', 8, 2);
            $table->timestamps();
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users');
            $table->foreign('id_pedido')
                ->references('id')
                ->on('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comissao_usuarios');
    }
};

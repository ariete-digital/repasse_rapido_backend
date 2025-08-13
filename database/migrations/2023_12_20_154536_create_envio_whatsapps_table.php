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
        Schema::create('envios_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('numero');
            $table->text('mensagem');
            $table->string('zaapId')->nullable();
            $table->string('messageId')->nullable();
            $table->dateTime('data_envio')->nullable();
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios_whatsapp');
    }
};

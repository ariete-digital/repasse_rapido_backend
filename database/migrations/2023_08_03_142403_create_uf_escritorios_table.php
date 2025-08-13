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
        Schema::create('ufs_escritorios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_uf');
            $table->unsignedBigInteger('id_escritorio_regional');
            $table->timestamps();
            $table->foreign('id_uf')
                ->references('id')
                ->on('ufs');
            $table->foreign('id_escritorio_regional')
                ->references('id')
                ->on('escritorios_regionais')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ufs_escritorios');
    }
};

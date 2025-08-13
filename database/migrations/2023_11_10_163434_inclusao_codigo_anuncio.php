<?php

use App\Models\Anuncio;
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
        Schema::table('anuncios', function (Blueprint $table) {
            $table->string('codigo')->index();
        });

        $anuncios = Anuncio::all();
        foreach ($anuncios as $anuncio) {
            $anuncio->codigo = Anuncio::geraCodigo();
            $anuncio->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};

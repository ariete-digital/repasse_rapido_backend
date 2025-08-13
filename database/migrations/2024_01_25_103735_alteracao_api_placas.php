<?php

use App\Models\Anuncio;
use App\Models\AnuncioRascunho;
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
        Schema::table('anuncios_rascunho', function (Blueprint $table) {
            // $table->dropForeign('anuncios_rascunho_id_modelo_foreign');
            // $table->dropColumn('id_modelo');
            $table->string('marca_veiculo')->nullable();
            $table->string('modelo_veiculo')->nullable();
            $table->string('submodelo')->nullable()->index();
            $table->string('valor_fipe')->nullable();
        });

        $anunciosRascunho = AnuncioRascunho::with('modelo.marca')->get();
        foreach ($anunciosRascunho as $key => &$anuncio) {
            if($anuncio->modelo && $anuncio->modelo->marca){
                $anuncio->marca_veiculo = $anuncio->modelo->marca->descricao;
            }
            if($anuncio->modelo){
                $anuncio->modelo_veiculo = $anuncio->modelo->descricao;
                $anuncio->submodelo = $anuncio->modelo->nome_curto;
            }
            $anuncio->save();
        }

        Schema::table('anuncios', function (Blueprint $table) {
            // $table->dropForeign('anuncios_id_modelo_foreign');
            // $table->dropColumn('id_modelo');
            $table->unsignedBigInteger('id_modelo')->nullable()->change();
            $table->string('marca_veiculo');
            $table->string('modelo_veiculo');
            $table->string('submodelo')->index();
            $table->string('valor_fipe')->nullable();
        });

        $anuncios = Anuncio::with('modelo.marca')->get();
        foreach ($anuncios as $key => &$anuncio) {
            $anuncio->marca_veiculo = $anuncio->modelo->marca->descricao;
            $anuncio->modelo_veiculo = $anuncio->modelo->descricao;
            $anuncio->submodelo = $anuncio->modelo->nome_curto;
            $anuncio->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios_rascunho', function (Blueprint $table) {
            // $table->unsignedBigInteger('id_modelo')->nullable();
            // $table->foreign('id_modelo')
            //     ->references('id')
            //     ->on('modelos');
            $table->dropColumn('marca_veiculo');
            $table->dropColumn('modelo_veiculo');
            $table->dropColumn('submodelo');
            $table->dropColumn('valor_fipe');
        });

        Schema::table('anuncios', function (Blueprint $table) {
            // $table->unsignedBigInteger('id_modelo')->nullable();
            // $table->foreign('id_modelo')
            //     ->references('id')
            //     ->on('modelos');
            $table->dropColumn('marca_veiculo');
            $table->dropColumn('modelo_veiculo');
            $table->dropColumn('submodelo');
            $table->dropColumn('valor_fipe');
        });
    }
};

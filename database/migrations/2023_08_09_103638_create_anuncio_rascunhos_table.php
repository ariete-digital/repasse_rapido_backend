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
        Schema::create('anuncios_rascunho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_anuncio_original')->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_modelo')->nullable();
            $table->unsignedBigInteger('id_cor')->nullable();
            $table->unsignedBigInteger('id_tipo_cambio')->nullable();
            $table->unsignedBigInteger('id_tipo_combustivel')->nullable();
            $table->unsignedBigInteger('id_tipo_pneu')->nullable();
            $table->unsignedBigInteger('id_tipo_parabrisa')->nullable();
            $table->string('tipo_plano', 1)->nullable(); // D-Destaque; A-Aberto; F-Fechado
            $table->string('tipo_venda', 1); // R-Repasse; C-Consumidor
            $table->string('tipo_vendedor', 1); // R-Revenda; P-Particular
            $table->string('tipo_veiculo', 1); // C-Carro; M-Moto; H-Caminhão
            $table->string('renavam')->nullable();
            $table->string('placa')->nullable();
            $table->string('status_veiculo')->nullable();
            $table->string('versao_veiculo')->nullable();
            $table->string('ano_fabricacao')->nullable();
            $table->string('ano_modelo')->nullable();
            $table->string('quilometragem')->nullable();
            $table->integer('num_portas')->nullable();
            $table->boolean('unico_dono')->nullable();
            $table->boolean('aceita_troca')->nullable();
            $table->boolean('ipva_pago')->nullable();
            $table->boolean('veiculo_nome_anunciante')->nullable();
            $table->boolean('financiado')->nullable();
            $table->boolean('parcelas_em_dia')->nullable();
            $table->boolean('todas_revisoes_concessionaria')->nullable();
            $table->boolean('passou_leilao')->nullable();
            $table->boolean('possui_manual')->nullable();
            $table->boolean('possui_chave_reserva')->nullable();
            $table->boolean('possui_ar')->nullable();
            $table->boolean('ar_funcionando')->nullable();
            $table->boolean('escapamento_solta_fumaca')->nullable();
            $table->boolean('garantia_fabrica')->nullable();
            $table->boolean('motor_bate')->nullable();
            $table->boolean('cambio_faz_barulho')->nullable();
            $table->boolean('cambio_escapa_marcha')->nullable();
            $table->boolean('luz_injecao_airbag')->nullable();
            $table->boolean('pequena_monta')->nullable();
            $table->boolean('media_monta')->nullable();
            $table->boolean('grande_monta')->nullable();
            $table->boolean('furtado_roubado')->nullable();
            $table->decimal('valor')->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('aceite_termos')->nullable();
            $table->timestamps();
            $table->foreign('id_anuncio_original')
                ->references('id')
                ->on('anuncios');
            $table->foreign('id_cliente')
                ->references('id')
                ->on('clientes');
            $table->foreign('id_modelo')
                ->references('id')
                ->on('modelos');
            $table->foreign('id_cor')
                ->references('id')
                ->on('cores');
            $table->foreign('id_tipo_cambio')
                ->references('id')
                ->on('tipos_cambio');
            $table->foreign('id_tipo_combustivel')
                ->references('id')
                ->on('tipos_combustivel');
            $table->foreign('id_tipo_pneu')
                ->references('id')
                ->on('tipos_pneu');
            $table->foreign('id_tipo_parabrisa')
                ->references('id')
                ->on('tipos_parabrisa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anuncios_rascunho');
    }
};

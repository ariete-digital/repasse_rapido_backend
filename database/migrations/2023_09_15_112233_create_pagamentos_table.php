<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pedido');
            $table->unsignedBigInteger('id_status');
            $table->unsignedBigInteger('id_forma');
            $table->decimal('valor');
            $table->string('codigo')->nullable()->index();
            $table->date('data_limite_vigencia')->nullable();
            $table->text('external_reference')->nullable();
            $table->string('status')->nullable();
            $table->string('status_detail')->nullable();
            $table->string('date_created')->nullable();
            $table->string('date_last_updated')->nullable();
            $table->string('date_of_expiration')->nullable();
            $table->string('date_approved')->nullable();
            $table->string('money_release_date')->nullable();
            $table->string('payment_method_id')->nullable();
            $table->string('payment_type_id')->nullable();
            $table->string('operation_type')->nullable();
            $table->boolean('captured')->nullable();
            $table->boolean('binary_mode')->nullable();
            $table->boolean('live_mode')->nullable();
            $table->string('collector_id')->nullable();
            $table->string('currency_id')->nullable();
            $table->integer('installments')->nullable();
            $table->decimal('installment_amount')->nullable();
            $table->string('token')->nullable();
            $table->text('qr_code')->nullable();
            $table->text('qr_code_base64')->nullable();
            $table->text('ticket_url')->nullable();
            $table->string('description')->nullable();
            $table->string('issuer_id')->nullable();
            $table->timestamps();
            $table->foreign('id_pedido')
                ->references('id')
                ->on('pedidos');
            $table->foreign('id_status')
                ->references('id')
                ->on('status_pagamentos');
            $table->foreign('id_forma')
                ->references('id')
                ->on('formas_pagamentos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagamentos');
    }
}

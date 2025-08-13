<?php

namespace App\Http\Controllers;

use App\Helpers\PagamentoHelper;
use App\Models\Anuncio;
use App\Models\Cliente;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\StatusPagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PagamentoController extends Controller
{
    public function notificacao(Request $request)
    {
        Log::info('executando funcao acionada pelo MP');
        Log::info(json_encode([
            'request' => $request->all()
        ]));
        switch($request->topic) {
            case "payment":
                $pagamentoMP = PagamentoHelper::buscarPagamento($request->id);
                // Log::info('pagamentoMP');
                // Log::info($pagamentoMP);
                $pagamentoBanco = Pagamento::where('codigo', $request->id)->with('pedido.cliente')->first();
                $statusAguardando = StatusPagamento::where('codigo', 'AGUARDANDO')->first();
                if($pagamentoMP['status'] == 'approved' && $pagamentoMP['captured'] && $statusAguardando->id == $pagamentoBanco->id_status){
                    $statusAprovado = StatusPagamento::where('codigo', 'APROVADO')->first();
                    $pagamentoBanco->id_status = $statusAprovado->id;
                    $pagamentoBanco->status = $pagamentoMP['status'];
                    $pagamentoBanco->status_detail = $pagamentoMP['status_detail'];
                    $pagamentoBanco->save();

                    // Log::info('pagamentoBanco');
                    // Log::info($pagamentoBanco->toArray());

                    $pedido = Pedido::where('id', $pagamentoBanco->id_pedido)->first();

                    $contAnuncios = Anuncio::where('id_cliente', $pagamentoBanco->pedido->cliente->id)->where('ativo', true)->where('pausado', false)->count();
                    PagamentoHelper::processaPagamentoAprovado($pagamentoBanco->pedido->cliente, $pagamentoBanco, $contAnuncios, $pedido);
                }

            break;
        }
    }
}

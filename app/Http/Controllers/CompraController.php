<?php

namespace App\Http\Controllers;

use App\Helpers\AnuncioHelper;
use App\Helpers\ComissaoHelper;
use App\Helpers\DtoHelper;
use App\Helpers\PagamentoHelper;
use App\Models\Anuncio;
use App\Models\AnuncioRascunho;
use App\Models\CarrinhoCompra;
use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\LicencaAnuncio;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Plano;
use App\Models\PrecoAnuncioPlano;
use App\Models\StatusPagamento;
use App\Models\Subregiao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    const SEPARADOR_IDENTIFICACAO = ';';
    
    public function salvarPlanoAnuncio(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $carrinhoCompra = CarrinhoCompra::updateOrCreate(
            [
                'id_cliente' => $cliente->id
            ],
            [
                'id_plano' => $request->id_plano,
                'quant_anuncios' => $request->quantidade,
            ]
        );
        return $this->getResponse('success', []);
    }

    public function obterInfoPlanoAnuncio(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();
        $carrinhoCompra = CarrinhoCompra::where('id_cliente', $cliente->id)->with('plano')->first();

        $precoPlano = PrecoAnuncioPlano::where('id_plano', $carrinhoCompra->id_plano)
            ->where('quant_anuncios', $carrinhoCompra->quant_anuncios)
            ->with('plano')
            ->first();
        
        if($precoPlano){
            return $this->getResponse('success', [
                'nome_plano' => $carrinhoCompra->plano->nome,
                'quant_anuncios' => $carrinhoCompra->quant_anuncios,
                'valor' => $precoPlano->preco,
                'payer' => DtoHelper::getPayerMPDTO($cliente),
                'mpPublicKey' => Config::get('mercadopago.public_key')
            ]);
        }
    }

    public function pagarPlanoAnuncio(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $carrinhoCompra = CarrinhoCompra::where('id_cliente', $cliente->id)->with('plano')->first();

        $contAnuncios = Anuncio::where('id_cliente', $cliente->id)->where('ativo', true)->where('pausado', false)->count();
        if($carrinhoCompra->quant_anuncios != -1 && $contAnuncios > $carrinhoCompra->quant_anuncios){
            return $this->getResponse('success', [
                'error' => true,
                'message' => 'Você possui mais anúncios ativos que a quantidade escolhida. Por favor, escolha uma quantidade maior.'
            ]);
        }

        $precoPlano = PrecoAnuncioPlano::where('id_plano', $carrinhoCompra->id_plano)
            ->where('quant_anuncios', $carrinhoCompra->quant_anuncios)
            ->with('plano')
            ->first();

        if($precoPlano){
            // Log::info($request->formData);

            // $retornoPagamento = PagamentoHelper::fazerPagamento(
            //     $request->formData,
            //     $precoPlano->preco,
            //     $request->deviceID,
            //     $carrinhoCompra->plano->nome
            // );

            //pagamento fake
            $retornoPagamento = (object)[
                'id' => bin2hex(random_bytes(16)),
                'status' => 'approved',
                'captured' => true,
                'installments' => 1,
                'external_reference' => null,
                'status_detail' => null,
                'date_created' => null,
                'date_last_updated' => null,
                'date_of_expiration' => null,
                'date_approved' => null,
                'money_release_date' => null,
                'payment_method_id' => null,
                'payment_type_id' => null,
                'operation_type' => null,
                'binary_mode' => null,
                'live_mode' => null,
                'collector_id' => null,
                'currency_id' => null,
                'description' => null,
                'issuer_id' => null,
                'transaction_details' => (object)[
                    'installment_amount' => 0
                ],
            ];


            // Log::info(json_encode($retornoPagamento));

            if(!$retornoPagamento){
                return $this->getResponse('success', [
                    'error' => 'Erro ao processar o pagamento',
                ]);
            }

            // return $this->getResponse('success', [
            //     'message' => 'Pagamento realizado com sucesso!'
            // ]);
            // $msgRetorno = PagamentoHelper::getMsgRetornoPagamento($retornoPagamento->status, $retornoPagamento->status_detail);

            $pedido = Pedido::create([
                'id_cliente' => $cliente->id,
                'nome_plano' => $carrinhoCompra->plano->nome,
                'tipo_plano' => $carrinhoCompra->plano->tipo,
                'quant_anuncios' => $carrinhoCompra->quant_anuncios,
            ]);

            $paymentType = 'CREDITO';
            if($request->paymentType == "bank_transfer") $paymentType = 'PIX';
            else if($request->paymentType == "ticket") $paymentType = 'BOLETO';

            $pagamento = PagamentoHelper::salvarPagamento($retornoPagamento, $paymentType, $precoPlano->preco, $pedido->id);

            // Log::info(json_encode($pagamento));

            PagamentoHelper::processaPagamentoAprovado($cliente, $pagamento, $contAnuncios, $pedido);

            $carrinhoCompra->delete();
            
        }

        return $this->getResponse('success', [
            'message' => 'Compra concluída com sucesso! Após a confirmação do pagamento, a quantidade de anúncios comprada estará disponível em sua conta para criação de anúncios.',
            'paymentId' => $retornoPagamento->id
        ]);
    }

    public function obterMinhasCompras(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        // $anunciosPublicados = Anuncio::where('id_cliente', $cliente->id)->get();
        // $anunciosPublicadosDTO = DtoHelper::getListaAnunciosDTO($anunciosPublicados, false, true);

        $pedidos = Pedido::where('id_cliente', $cliente->id)->with('anuncio.modelo.marca', 'pagamento')->orderBy('created_at', 'desc')->get();
        // Log::info(json_encode([
        //     'pedidos' => $pedidos,
        // ]));
        // $pedidosAnuncioPF = [];
        // $pedidosPlano = [];
        // $pedidosContato = [];
        /**
         * Casos possíveis na listagem de pedidos:
         *  1 - id_anuncio is null => compra de plano PJ
         *  2 - id_cliente == anuncios.id_cliente => anuncio PF
         *  3 - id_cliente != anuncios.id_cliente => compra de contato de vendedor
         */
        // foreach ($pedidos as $key => $pedido) {
        //     if(!$pedido->anuncio){
        //         array_push($pedidosPlano, $pedido);
        //     } else if($pedido->anuncio->id_cliente == $pedido->id_cliente){
        //         array_push($pedidosAnuncioPF, $pedido);
        //     } else if($pedido->anuncio->id_cliente != $pedido->id_cliente){
        //         array_push($pedidosContato, $pedido);
        //     }
        // }

        // $pedidosAnuncioPFDTO = DtoHelper::getListaPedidosDTO($pedidosAnuncioPF, true);
        // $pedidosPlanoDTO = DtoHelper::getListaPedidosDTO($pedidosPlano, true);
        // $pedidosContatoDTO = DtoHelper::getListaPedidosDTO($pedidosContato, true);

        $pedidosDTO = DtoHelper::getListaPedidosDTO($pedidos, true);

        return $this->getResponse('success', [
            'pedidos' => $pedidosDTO,
        ]);
    }

    public function obterDetalhesMinhasCompras(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->with('pagamento.statusPagamento', 'pagamento.formaPagamento', 'cliente.usuario', 'anuncio')->first();

        return $this->getResponse('success', [
            'pedido' => DtoHelper::getPedidoDTO($pedido, true)
        ]);
    }

    public function obterInfoPagamentoAnuncioFechado(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();

        $anuncio = Anuncio::where('id', $request->id)->first();
        $precoPlano = PrecoAnuncioPlano::whereRelation('plano', 'tipo', '=', $anuncio->tipo_plano)
            ->where('quant_anuncios', 1)
            ->with('plano')
            ->first();
        
        return $this->getResponse('success', [
            'nome_plano' => $anuncio->tipo_plano_str,
            'valor' => $precoPlano->preco,
            'payer' => DtoHelper::getPayerMPDTO($cliente),
            'mpPublicKey' => Config::get('mercadopago.public_key')
        ]);
    }

    public function salvarPagamentoAnuncioFechado(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $anuncio = Anuncio::where('id', $request->id_anuncio)
            ->with('cliente.usuario', 'cliente.cidade.estado')
            ->first();

        $precoPlano = PrecoAnuncioPlano::whereRelation('plano', 'tipo', '=', 'F')
            ->where('quant_anuncios', 1)
            ->with('plano')
            ->first();   
        $retornoPagamento = PagamentoHelper::fazerPagamento(
            $request->formData,
            $precoPlano->preco,
            $request->deviceID,
            $precoPlano->plano->nome
        );
        
        $pedido = Pedido::create([
            'id_cliente' => $cliente->id,
            'id_anuncio' => $anuncio->id,
            'marca_modelo' => $anuncio->modelo->marca->descricao . " " . $anuncio->modelo->descricao,
            // 'versao_veiculo' => $anuncio->versao_veiculo,
            'nome_proprietario' => $anuncio->cliente->usuario->nome,
            'localizacao_proprietario' => $anuncio->cliente->cidade->nome . " (" . $anuncio->cliente->cidade->estado->sigla . ")",
            'telefone_proprietario' => $anuncio->cliente->telefone,
            'celular_proprietario' => $anuncio->cliente->celular,
        ]);
        
        $paymentType = 'CREDITO';
        if($request->paymentType == "bank_transfer") $paymentType = 'PIX';
        else if($request->paymentType == "ticket") $paymentType = 'BOLETO';

        $pagamento = PagamentoHelper::salvarPagamento($retornoPagamento, $paymentType, $precoPlano->preco, $pedido->id);

        $existePedido = false;
        $statusAprovado = StatusPagamento::where('codigo', 'APROVADO')->first();
        if($pagamento->id_status == $statusAprovado->id){
            // ComissaoHelper::salvarComissaoVenda($pedido->id, $cliente->cidade->estado->id, $cliente->cep, $precoPlano->preco);
            $existePedido = true;
        }

        return $this->getResponse('success', [
            'message' => 'Compra realizada com sucesso!',
            'paymentId' => $retornoPagamento->id,
            'existePedido' => $existePedido
        ]);
    }

    public function obterInfoPagamentoCriacaoAnuncio(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();

        $anuncioRascunho = AnuncioRascunho::where('id', $request->id)->first();
        $precoPlano = PrecoAnuncioPlano::whereRelation('plano', 'tipo', '=', $anuncioRascunho->tipo_plano)
            ->where('quant_anuncios', 1)
            ->with('plano')
            ->first();
        
        return $this->getResponse('success', [
            'nome_plano' => $anuncioRascunho->tipo_plano_str,
            'valor' => $precoPlano->preco,
            'payer' => DtoHelper::getPayerMPDTO($cliente),
            'mpPublicKey' => Config::get('mercadopago.public_key')
        ]);
    }

    public function fazerPagamentoCriacaoAnuncio(Request $request)
    {
        //fazer pagamento
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();
        $anuncioRascunho = AnuncioRascunho::where('id', $request->id_anuncio_rascunho)
            ->with('opcionais', 'imagens')
            ->first();
        
        $precoPlano = PrecoAnuncioPlano::whereRelation('plano', 'tipo', '=', $anuncioRascunho->tipo_plano)
            ->where('quant_anuncios', 1)
            ->with('plano')
            ->first();

        // Log::info('request->formData');
        // Log::info($request->formData);
        // $retornoPagamento = PagamentoHelper::fazerPagamento(
        //     $request->formData,
        //     $precoPlano->preco,
        //     $request->deviceID,
        //     $precoPlano->plano->nome
        // );

        //pagamento fake
        $retornoPagamento = (object)[
            'id' => bin2hex(random_bytes(16)),
            'status' => 'approved',
            'captured' => true,
            'installments' => 1,
            'external_reference' => null,
            'status_detail' => null,
            'date_created' => null,
            'date_last_updated' => null,
            'date_of_expiration' => null,
            'date_approved' => null,
            'money_release_date' => null,
            'payment_method_id' => null,
            'payment_type_id' => null,
            'operation_type' => null,
            'binary_mode' => null,
            'live_mode' => null,
            'collector_id' => null,
            'currency_id' => null,
            'description' => null,
            'issuer_id' => null,
            'transaction_details' => (object)[
                'installment_amount' => 0
            ],
        ];

        if(!$retornoPagamento){
            return $this->getResponse('success', [
                'error' => 'Erro ao processar o pagamento',
            ]);
        }
        
        $anuncio = AnuncioHelper::gerarAnuncioDoRascunho($anuncioRascunho);
        $anuncio->load('modelo.marca');
        $anuncio->pausado = 1;
        $anuncio->save();
        // Log::info($anuncio);
        $anuncio->data_validade = Carbon::now()->addDays(30);
        $anuncio->save();
        
        $pedido = Pedido::create([
            'id_cliente' => $cliente->id,
            'id_anuncio' => $anuncio->id,
            'marca_modelo' => $anuncio->marca_veiculo . ' ' . $anuncio->modelo_veiculo,
            // 'versao_veiculo' => $anuncio->versao_veiculo,
            'nome_proprietario' => $cliente->usuario->nome,
            'localizacao_proprietario' => $cliente->cidade->nome . " (" . $cliente->cidade->estado->sigla . ")",
            'telefone_proprietario' => $cliente->telefone,
            'celular_proprietario' => $cliente->celular,
        ]);

        $paymentType = 'CREDITO';
        if($request->paymentType == "bank_transfer") $paymentType = 'PIX';
        else if($request->paymentType == "ticket") $paymentType = 'BOLETO';

        $pagamento = PagamentoHelper::salvarPagamento($retornoPagamento, $paymentType, $precoPlano->preco, $pedido->id);

        $statusAprovado = StatusPagamento::where('codigo', 'APROVADO')->first();
        if($pagamento->id_status == $statusAprovado->id){
            $anuncio->pausado = 0;
            $anuncio->save();
            // ComissaoHelper::salvarComissaoVenda($pedido->id, $cliente->cidade->estado->id, $cliente->cep, $precoPlano->preco);
        }

        return $this->getResponse('success', [
            'message' => 'Compra realizada com sucesso!',
            'paymentId' => $retornoPagamento->id
        ]);
    }

    public function obterInfoPagamentoExtra(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();
        $carrinhoCompra = CarrinhoCompra::where('id_cliente', $cliente->id)->with('plano')->first();

        $precoPlano = PrecoAnuncioPlano::where('id_plano', $carrinhoCompra->id_plano)
            ->where('quant_anuncios', $carrinhoCompra->quant_anuncios)
            ->with('plano')
            ->first();
        
        if($precoPlano){
            return $this->getResponse('success', [
                'nome_plano' => $carrinhoCompra->plano->nome,
                'quant_anuncios' => $carrinhoCompra->quant_anuncios,
                'valor' => $precoPlano->preco,
                'payer' => DtoHelper::getPayerMPDTO($cliente),
                'mpPublicKey' => Config::get('mercadopago.public_key')
            ]);
        }
    }

    public function fazerPagamentoExtra(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();
        $carrinhoCompra = CarrinhoCompra::where('id_cliente', $cliente->id)->with('plano')->first();
        $anuncio = Anuncio::where('id', $carrinhoCompra->id_anuncio)->first();
        
        $precoPlano = PrecoAnuncioPlano::where('id_plano', $carrinhoCompra->id_plano)
            ->where('quant_anuncios', $carrinhoCompra->quant_anuncios)
            ->with('plano')
            ->first();

        if($precoPlano){
            // Log::info($request->formData);

            // $retornoPagamento = PagamentoHelper::fazerPagamento(
            //     $request->formData,
            //     $precoPlano->preco,
            //     $request->deviceID,
            //     $carrinhoCompra->plano->nome . CompraController::SEPARADOR_IDENTIFICACAO . $carrinhoCompra->info_extra
            // );

            //pagamento fake
            $retornoPagamento = (object)[
                'id' => bin2hex(random_bytes(16)),
                'status' => 'approved',
                'captured' => true,
                'installments' => 1,
                'external_reference' => null,
                'status_detail' => null,
                'date_created' => null,
                'date_last_updated' => null,
                'date_of_expiration' => null,
                'date_approved' => null,
                'money_release_date' => null,
                'payment_method_id' => null,
                'payment_type_id' => null,
                'operation_type' => null,
                'binary_mode' => null,
                'live_mode' => null,
                'collector_id' => null,
                'currency_id' => null,
                'description' => null,
                'issuer_id' => null,
                'transaction_details' => (object)[
                    'installment_amount' => 0
                ],
            ];

            // Log::info(json_encode($retornoPagamento));

            if(!$retornoPagamento){
                return $this->getResponse('success', [
                    'error' => 'Erro ao processar o pagamento',
                ]);
            }

            // return $this->getResponse('success', [
            //     'message' => 'Pagamento realizado com sucesso!'
            // ]);
            // $msgRetorno = PagamentoHelper::getMsgRetornoPagamento($retornoPagamento->status, $retornoPagamento->status_detail);

            $pedido = Pedido::create([
                'id_cliente' => $cliente->id,
                'nome_plano' => $carrinhoCompra->plano->nome,
                'tipo_plano' => $carrinhoCompra->plano->tipo,
                'quant_anuncios' => $carrinhoCompra->quant_anuncios,
                'id_anuncio' => $carrinhoCompra->id_anuncio,
                'info_extra' => $carrinhoCompra->info_extra,
                'marca_modelo' => $anuncio->marca_veiculo . ' ' . $anuncio->modelo_veiculo,
                'nome_proprietario' => $cliente->usuario->nome,
                'localizacao_proprietario' => $cliente->cidade->nome . " (" . $cliente->cidade->estado->sigla . ")",
                'telefone_proprietario' => $cliente->telefone,
                'celular_proprietario' => $cliente->celular,
            ]);

            $paymentType = 'CREDITO';
            if($request->paymentType == "bank_transfer") $paymentType = 'PIX';
            else if($request->paymentType == "ticket") $paymentType = 'BOLETO';

            $pagamento = PagamentoHelper::salvarPagamento($retornoPagamento, $paymentType, $precoPlano->preco, $pedido->id);

            // Log::info(json_encode($pagamento));

            PagamentoHelper::processaPagamentoAprovado($cliente, $pagamento, 0, $pedido);

            $carrinhoCompra->delete();
            
        }

        return $this->getResponse('success', [
            'message' => 'Compra concluída com sucesso! Aguarde a confirmação de pagamento',
            'paymentId' => $retornoPagamento->id
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\IntegracaoNfDtoHelper;
use App\Models\Cliente;
use App\Models\ComissaoUsuario;
use App\Models\Pedido;
use DateTime;
use Illuminate\Http\Request;

class IntegracaoNFController extends Controller
{
    public function obterClientesV1(Request $request)
    {
        $clientes = Cliente::with('usuario', 'cidade.estado')->has('pedidos');
        if($request->data_inicial){
            $dataInicial = DateTime::createFromFormat('Y-m-d', $request->data_inicial);
            if(!$dataInicial){
                $dataInicial = DateTime::createFromFormat('Y-m-d-H-i', $request->data_inicial);
            }
            $clientes = $clientes->where('created_at', '>=', $dataInicial);
        }
        if($request->data_final){
            $dataFinal = DateTime::createFromFormat('Y-m-d', $request->data_final);
            if(!$dataFinal){
                $dataFinal = DateTime::createFromFormat('Y-m-d-H-i', $request->data_final);
            }
            $clientes = $clientes->where('created_at', '<=', $dataFinal);
        }
        $clientes = $clientes->get();
        $clientesDTO = IntegracaoNfDtoHelper::getListaClientesDTO($clientes);

        return $this->getResponse('success', [
            'clientes' => $clientesDTO
        ]);
    }

    public function obterPedidosV1(Request $request)
    {
        $pedidos = Pedido::with('cliente.usuario', 'cliente.cidade.estado','pagamento.statusPagamento', 'pagamento.formaPagamento', 'anuncio');
        if($request->data_inicial){
            $dataInicial = DateTime::createFromFormat('Y-m-d', $request->data_inicial);
            if(!$dataInicial){
                $dataInicial = DateTime::createFromFormat('Y-m-d-H-i', $request->data_inicial);
            }
            $pedidos = $pedidos->where('created_at', '>=', $dataInicial);
        }
        if($request->data_final){
            $dataFinal = DateTime::createFromFormat('Y-m-d', $request->data_final);
            if(!$dataFinal){
                $dataFinal = DateTime::createFromFormat('Y-m-d-H-i', $request->data_final);
            }
            $pedidos = $pedidos->where('created_at', '<=', $dataFinal);
        }
        $pedidos = $pedidos->get();
        $pedidosDTO = IntegracaoNfDtoHelper::getListaPedidosDTO($pedidos);

        return $this->getResponse('success', [
            'pedidos' => $pedidosDTO
        ]);
    }

    public function obterComissoesV1(Request $request)
    {
        $comissoes = ComissaoUsuario::with('usuario');
        if($request->data_inicial){
            $dataInicial = DateTime::createFromFormat('Y-m-d', $request->data_inicial);
            if(!$dataInicial){
                $dataInicial = DateTime::createFromFormat('Y-m-d-H-i', $request->data_inicial);
            }
            $comissoes = $comissoes->where('created_at', '>=', $dataInicial);
        }
        if($request->data_final){
            $dataFinal = DateTime::createFromFormat('Y-m-d', $request->data_final);
            if(!$dataFinal){
                $dataFinal = DateTime::createFromFormat('Y-m-d-H-i', $request->data_final);
            }
            $comissoes = $comissoes->where('created_at', '<=', $dataFinal);
        }
        $comissoes = $comissoes->get();
        $comissoesDTO = IntegracaoNfDtoHelper::getListaComissaoDTO($comissoes);

        return $this->getResponse('success', [
            'comissoes' => $comissoesDTO
        ]);
    }
}

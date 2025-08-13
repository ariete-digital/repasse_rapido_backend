<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Paciente;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    const NUM_REG_POR_PAG = 10;
    const NUM_CASOS_POR_PAG = 10;
    const NUM_PEDIDOS_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Pedido::where('codigo', 'LIKE', '%' . $request->filtro . '%')
                ->with('pagamento.statusPagamento', 'pagamento.formaPagamento')
                ->orderBy('created_at','desc')
                ->paginate(PedidoController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Pedido::with('pagamento.statusPagamento', 'pagamento.formaPagamento')
                ->orderBy('created_at','desc')
                ->paginate(PedidoController::NUM_REG_POR_PAG);
        }
        $pedidos = $paginacao->items();
        $listaPedidosDTO = DtoHelper::getListaPedidosDTO($pedidos, true);
        return $this->getResponse('success', [
            'listaPedidos' => $listaPedidosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obterDetalhePedido(Request $request)
    {
        $pedido = Pedido::where('id', $request->id)->with('pagamento.statusPagamento', 'pagamento.formaPagamento', 'cliente.usuario')->first();

        return $this->getResponse('success', [
            'pedido' => DtoHelper::getPedidoDTO($pedido, true)
        ]);
    }

    public function obterListaCasos(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Paciente::where('id_usuario', Auth::id())
                ->where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->has('pedidos')
                ->paginate(PedidoController::NUM_CASOS_POR_PAG);
        } else {
            $paginacao = Paciente::where('id_usuario', Auth::id())
                ->has('pedidos')
                ->paginate(PedidoController::NUM_CASOS_POR_PAG);
        }

        $pacientes = $paginacao->items();
        $listaPacientesDTO = [];
        foreach ($pacientes as $key => $paciente) {
            $pacienteDTO = [
                'id' => $paciente->id,
                'nome' => $paciente->nome,
            ];
            array_push($listaPacientesDTO, $pacienteDTO);
        }

        $primeiroNome = substr(Auth::user()->name, 0, strpos(Auth::user()->name, " "));
        $nomeUsuarioLogado = ucfirst($primeiroNome);

        return $this->getResponse('success', [
            'listaCasos' => $listaPacientesDTO,
            'nomeUsuarioLogado' => $nomeUsuarioLogado,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obterListaPedidos(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Pedido::where('id_paciente', $request->id_paciente)
                ->where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(PedidoController::NUM_PEDIDOS_POR_PAG);
        } else {
            $paginacao = Pedido::where('id_paciente', $request->id_paciente)
                ->paginate(PedidoController::NUM_PEDIDOS_POR_PAG);
        }

        $pedidos = $paginacao->items();
        $listaPedidosDTO = [];
        foreach ($pedidos as $key => $pedido) {
            $pedidoDTO = [
                'id' => $pedido->id,
                'nome' => $pedido->nome,
                'codigo' => $pedido->codigo,
                'data_criacao' => $pedido->data_criacao,
            ];
            array_push($listaPedidosDTO, $pedidoDTO);
        }

        $paciente = Paciente::where('id', $request->id_paciente)->first();
        $pacienteDTO = DtoHelper::getPacienteDTO($paciente);

        return $this->getResponse('success', [
            'listaPedidos' => $listaPedidosDTO,
            'paciente' => $pacienteDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }
}

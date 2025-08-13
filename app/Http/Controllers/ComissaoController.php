<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\ComissaoUsuario;
use App\Models\User;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComissaoController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function listaMinhasComissoes(Request $request)
    {
        return $this->getComissoes(Auth::id(), $request->dataInicio, $request->dataFim, $request->tipoPessoa);
    }

    public function listaComissoes(Request $request)
    {
        return $this->getComissoes($request->idUsuario, $request->dataInicio, $request->dataFim, $request->tipoPessoa);
    }

    public function listaUsuarios(Request $request)
    {
        $paginacao = User::whereIn('role', [User::CODIGO_GERENTE, User::CODIGO_VENDEDOR])->paginate(UsuarioController::NUM_REG_POR_PAG);
        $usuarios = $paginacao->items();
        $listaUsuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);
        
        return $this->getResponse('success', [
            'listaUsuarios' => $listaUsuariosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    private function getComissoes($idUsuario, $dataInicio, $dataFim, $tipoPessoa)
    {
        $comissoes = ComissaoUsuario::where("id_usuario", $idUsuario);
        if($dataInicio){
            $dataInicio = DateTime::createFromFormat("d-m-Y", $dataInicio);
            $dataInicio->setTime(0,0,0);
            $comissoes = $comissoes->where("created_at", ">=", $dataInicio);
        }
        if($dataFim){
            $dataFim = DateTime::createFromFormat("d-m-Y", $dataFim);
            $dataFim->setTime(23,59,59);
            $comissoes = $comissoes->where("created_at", "<=", $dataFim);
        }
        if($tipoPessoa && $tipoPessoa != "TODOS"){
            $comissoes = $comissoes->whereRelation("pedido.cliente", "tipo", "=", $tipoPessoa);
        }
        $comissoes = $comissoes->with('pedido.pagamento', 'anuncio')->orderBy('created_at', 'desc')->get();
        $comissoesDTO = [];
        $total = 0;

        if(count($comissoes) > 0) {
            $comissoesDTO = DtoHelper::getListaComissoesDTO($comissoes);
            $total = $comissoes->sum('valor');
        }
        
        return $this->getResponse('success', [
            'listaComissoes' => $comissoesDTO,
            'valorTotal' => "R$ " . number_format($total, 2, ',', '.'),
        ]);
    }
}

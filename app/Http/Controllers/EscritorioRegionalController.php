<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\ComissaoUsuario;
use App\Models\EscritorioRegional;
use App\Models\Uf;
use App\Models\UfEscritorio;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EscritorioRegionalController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = EscritorioRegional::where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->with('ufs', 'usuario')
                ->paginate(EscritorioRegionalController::NUM_REG_POR_PAG);
        } else {
            $paginacao = EscritorioRegional::paginate(EscritorioRegionalController::NUM_REG_POR_PAG);
        }
        $escritorios = $paginacao->items();
        $listaEscritoriosDTO = DtoHelper::getListaEscritoriosDTO($escritorios, true);
        return $this->getResponse('success', [
            'listaEscritorios' => $listaEscritoriosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $escritorioDTO = [];
        if ($request->id) {
            $escritorio = EscritorioRegional::where('id', $request->id)->first();
            $escritorioDTO = DtoHelper::getEscritorioDTO($escritorio, true);
        } else {
            $escritorioDTO = [
                'id' => 0,
                'nome' => '',
                'endereco' => '',
                'email' => '',
                'telefone' => '',
                'percentual_comissao' => '',
                'ufs' => [],
            ];
        }

        $ufs = Uf::orderBy('nome')->get();
        $ufsDTO = DtoHelper::getListaUfsDTO($ufs);

        $usuarios = User::where('role', User::CODIGO_GERENTE)->where('active', true)->orderBy('nome')->get();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);

        return $this->getResponse('success', [
            'escritorio' => $escritorioDTO,
            'ufs' => $ufsDTO,
            'usuarios' => $usuariosDTO,
        ]);
    }

    public function obterDetalhes(Request $request)
    {
        $idEscritorio = null;
        if($request->id){
            $idEscritorio = $request->id;
        } else {
            $usuario = User::where('id', Auth::id())->with('escritorio')->first();
            $idEscritorio = $usuario->escritorio->id;
        }
        $escritorio = EscritorioRegional::where('id', $idEscritorio)->with('usuario')->first();
        $escritorioDTO = DtoHelper::getEscritorioDTO($escritorio, true);

        $paginacao = User::whereRelation('subregiao', 'id_escritorio_regional', '=', $escritorio->id)->with('subregiao')->orderBy('nome')->paginate(EscritorioRegionalController::NUM_REG_POR_PAG);
        $usuarios = $paginacao->items();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios, true);

        $mesAtual = Carbon::now()->startOfMonth()->setTime(0, 0, 0);
        $comissaoMes = ComissaoUsuario::where('id_usuario', $escritorio->id_usuario)->where('created_at', '>=', $mesAtual)->sum('valor');

        return $this->getResponse('success', [
            'comissaoMes' => $comissaoMes,
            'escritorio' => $escritorioDTO,
            'usuarios' => $usuariosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'percentual_comissao' => 'required',
        ]);

        $escritorio = new EscritorioRegional();
        if ($request->id != null) {
            $escritorio = EscritorioRegional::where('id', $request->id)->first();
            $escritorio->nome = $request->nome;
            $escritorio->endereco = $request->endereco;
            $escritorio->email = $request->email;
            $escritorio->telefone = $request->telefone;
            $escritorio->percentual_comissao = $request->percentual_comissao;
            $escritorio->id_usuario = $request->id_usuario;
            $escritorio->save();
        } else {
            $escritorio = EscritorioRegional::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'nome' => $request->nome,
                    'endereco' => $request->endereco,
                    'email' => $request->email,
                    'telefone' => $request->telefone,
                    'id_usuario' => $request->id_usuario,
                    'percentual_comissao' => $request->percentual_comissao,
                ]
            );
        }

        UfEscritorio::where('id_escritorio_regional', $escritorio->id)->whereNotIn('id_uf', $request->ufs)->delete();
        foreach ($request->ufs as $key => $uf) {
            UfEscritorio::updateOrCreate(
                [
                    'id_uf' => $uf,
                    'id_escritorio_regional' => $escritorio->id
                ],
                []
            );
        }

        return $this->getResponse('success', [
            'message' => "Escritório salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $escritorio = EscritorioRegional::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Escritório excluído com sucesso!"
        ]);
    }

    public function listagemInfo(Request $request)
    {
        $ufs = Uf::orderBy('nome')->get();
        $ufsDTO = DtoHelper::getListaUfsDTO($ufs);
        
        $usuarios = User::where('role', User::CODIGO_GERENTE)->where('active', true)->orderBy('nome')->get();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);
        
        return $this->getResponse('success', [
            'ufs' => $ufsDTO,
            'usuarios' => $usuariosDTO,
        ]);
    }

    public function obterMeuEscritorio(Request $request)
    {
        $escritorio = EscritorioRegional::where('id_usuario', Auth::id())->first();        
        $escritorioDTO = DtoHelper::getEscritorioDTO($escritorio, true);

        return $this->getResponse('success', [
            'escritorio' => $escritorioDTO,
        ]);
    }
}

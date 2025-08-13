<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cliente;
use App\Models\ComissaoUsuario;
use App\Models\EscritorioRegional;
use App\Models\LocalidadeSubregiao;
use App\Models\Subregiao;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubregiaoController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        $paginacao = Subregiao::with('ufs', 'faixasCep', 'usuario');
        if ($request->filtro) {
            $paginacao = $paginacao->where('nome', 'LIKE', '%' . $request->filtro . '%');
        }
        if ($request->idEscritorio) {
            $paginacao = $paginacao->where('id_escritorio_regional', $request->idEscritorio);
        }
        $paginacao = $paginacao->paginate(SubregiaoController::NUM_REG_POR_PAG);
        $subregioes = $paginacao->items();
        $listaSubregioesDTO = DtoHelper::getListaSubregioesDTO($subregioes, true, true);
        return $this->getResponse('success', [
            'listaSubregioes' => $listaSubregioesDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $subregiaoDTO = [];
        if ($request->id) {
            $subregiao = Subregiao::where('id', $request->id)->with('ufs', 'faixasCep', 'usuario')->first();
            $subregiaoDTO = DtoHelper::getSubregiaoDTO($subregiao, true, true);
        } else {
            $subregiaoDTO = [
                'id' => 0,
                'nome' => '',
                'endereco' => '',
                'email' => '',
                'telefone' => '',
                'percentual_comissao' => '',
                'ufs' => [],
                'faixas_cep' => [],
            ];
        }

        $escritorios = EscritorioRegional::all();
        $escritoriosDTO = DtoHelper::getListaEscritoriosDTO($escritorios, true);

        $usuarios = User::where('role', User::CODIGO_VENDEDOR)->where('active', true)->orderBy('nome')->get();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);

        return $this->getResponse('success', [
            'subregiao' => $subregiaoDTO,
            'escritorios' => $escritoriosDTO,
            'usuarios' => $usuariosDTO,
        ]);
    }

    public function obterDetalhes(Request $request)
    {
        $subregiao = Subregiao::where('id', $request->id)->with('usuario', 'ufs', 'faixasCep')->first();
        $subregiaoDTO = DtoHelper::getSubregiaoDTO($subregiao, true);

        $idsUfs = $subregiao->ufs->pluck('id');
        $clientes = Cliente::with('usuario')->whereHas('cidade', function (Builder $query) use ($idsUfs){
            $query->whereIn('id_uf', $idsUfs);
        });
        if($request->filtro){
            $clientes = $clientes->where('nome', 'LIKE', '%'.$request->filtro.'%');
        }
        $clientes = $clientes->get();

        foreach ($subregiao->faixasCep as $key => $faixa) {
            $cepInicial = intval($faixa['cep_inicial']);
            $cepFinal = intval($faixa['cep_final']);
            $listaClientes = Cliente::where(DB::raw("CONVERT(REPLACE(REPLACE(cep, '-', ''), '.', ''), UNSIGNED)"), '>=' , $cepInicial)
                ->where(DB::raw("CONVERT(REPLACE(REPLACE(cep, '-', ''), '.', ''), UNSIGNED)"), '<' , $cepFinal)
                ->with('usuario');
            if($request->filtro){
                $listaClientes = $listaClientes->where('nome', 'LIKE', '%'.$request->filtro.'%');
            }
            $listaClientes = $listaClientes->get();
            $clientes = $clientes->concat($listaClientes);
        }
        $listaClientesDTO = DtoHelper::getListaClientesDTO($clientes);

        $mesAtual = Carbon::now()->startOfMonth()->setTime(0, 0, 0);
        $comissaoMes = ComissaoUsuario::where('id_usuario', $subregiao->id_usuario)->where('created_at', '>=', $mesAtual)->sum('valor');

        return $this->getResponse('success', [
            'comissaoMes' => $comissaoMes,
            'subregiao' => $subregiaoDTO,
            'listaClientes' => $listaClientesDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'percentual_comissao' => 'required',
            'id_escritorio_regional' => 'required',
        ]);

        $subregiao = new Subregiao();
        if ($request->id != null) {
            $subregiao = Subregiao::where('id', $request->id)->first();
            $subregiao->nome = $request->nome;
            $subregiao->endereco = $request->endereco;
            $subregiao->email = $request->email;
            $subregiao->telefone = $request->telefone;
            $subregiao->percentual_comissao = $request->percentual_comissao;
            $subregiao->id_escritorio_regional = $request->id_escritorio_regional;
            $subregiao->id_usuario = $request->id_usuario;
            $subregiao->save();
        } else {
            $subregiao = Subregiao::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'nome' => $request->nome,
                    'endereco' => $request->endereco,
                    'email' => $request->email,
                    'telefone' => $request->telefone,
                    'percentual_comissao' => $request->percentual_comissao,
                    'id_escritorio_regional' => $request->id_escritorio_regional,
                    'id_usuario' => $request->id_usuario,
                ]
            );
        }

        LocalidadeSubregiao::where('id_subregiao', $subregiao->id)->whereNotNull('id_uf')->whereNotIn('id_uf', $request->ufs)->delete();
        foreach ($request->ufs as $key => $uf) {
            LocalidadeSubregiao::updateOrCreate(
                [
                    'id_uf' => $uf,
                    'id_subregiao' => $subregiao->id
                ],
                []
            );
        }

        LocalidadeSubregiao::where('id_subregiao', $subregiao->id)->whereNull('id_uf')->delete();
        foreach ($request->faixas_cep as $key => $faixa) {
            LocalidadeSubregiao::create([
                'id_subregiao' => $subregiao->id,
                'cep_inicial' => $faixa['cep_inicial'],
                'cep_final' => $faixa['cep_final'],
            ]);
        }

        return $this->getResponse('success', [
            'message' => "Subregião salva com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $subregiao = Subregiao::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Subregião excluída com sucesso!"
        ]);
    }

    public function listagemInfo(Request $request)
    {
        $escritorios = EscritorioRegional::all();
        $escritoriosDTO = DtoHelper::getListaEscritoriosDTO($escritorios, true);

        $usuarios = User::where('role', User::CODIGO_VENDEDOR)->where('active', true)->orderBy('nome')->get();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);

        return $this->getResponse('success', [
            'escritorios' => $escritoriosDTO,
            'usuarios' => $usuariosDTO,
        ]);
    }
}

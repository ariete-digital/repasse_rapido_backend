<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cliente;
use App\Models\Plano;
use App\Models\PrecoAnuncioPlano;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PlanoController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Plano::where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->with('precos')
                ->paginate(PlanoController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Plano::with('precos')->paginate(PlanoController::NUM_REG_POR_PAG);
        }
        $planos = $paginacao->items();
        $listaPlanosDTO = DtoHelper::getListaPlanosDTO($planos);
        return $this->getResponse('success', [
            'listaPlanos' => $listaPlanosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $planoDTO = [];
        if ($request->id) {
            $plano = Plano::where('id', $request->id)->with('precos')->first();
            $planoDTO = DtoHelper::getPlanoDTO($plano);
        } else {
            $planoDTO = [
                'id' => 0,
                'nome' => '',
                'descricao' => '',
                'precos' => []
            ];
        }
        return $this->getResponse('success', [
            'plano' => $planoDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        Log::info(json_encode($request->all()));
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
        ]);

        $plano = new Plano();
        if ($request->id != null) {
            $plano = Plano::where('id', $request->id)->first();
            $plano->nome = $request->nome;
            $plano->descricao = $request->descricao;
            $plano->descricao_curta = $request->descricao_curta;
            $plano->tipo = $request->tipo;
            $plano->save();
        } else {
            $plano = Plano::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'nome' => $request->nome,
                    'descricao' => $request->descricao,
                    'descricao_curta' => $request->descricao_curta,
                    'tipo' => $request->tipo,
                ]
            );
        }

        if($request->precos){
            foreach ($request->precos as $key => $preco) {
                $idPreco = isset($preco['id']) ? $preco['id'] : null;
                $valor = str_replace('.', '', $preco['preco']);
                $valor = str_replace(',', '.', $valor);
                $p = PrecoAnuncioPlano::updateOrCreate(
                    ['id' => $idPreco],
                    [
                        'quant_anuncios' => $preco['quant_anuncios'],
                        'preco' => (float)$valor,
                        'id_plano' => $plano->id
                    ]
                );
            }
        }

        if($request->idsPrecosRemovidos){
            foreach ($request->idsPrecosRemovidos as $key => $id) {
                PrecoAnuncioPlano::where('id', $id)->delete();
            }
        }

        return $this->getResponse('success', [
            'message' => "Plano salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $plano = Plano::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Plano excluído com sucesso!"
        ]);
    }

    public function listagemPlanos(Request $request)
    {
        $planos = [];
        $cliente = Cliente::where("id_usuario", Auth::id())->first();
        if((!Auth::check() && $request->origem == 'compra_plano') || (Auth::check() && $request->origem == 'compra_plano' && $cliente && $cliente->tipo == 'PF')) {
            return $this->getResponse('unauthorized', [], 401);
        }

        // Log::info($cliente);

        $planos = Plano::where('tipo', 'A')->with('precos')->get();
        // if($request->tipoPlanoFiltro){
        //     $planos = $planos->where('tipo', $request->tipoPlanoFiltro);
        // } else if($cliente && $cliente->tipo == "PJ"){
        //     $planos = $planos->where('tipo', '<>', 'F');
        // }
        // $planos = $planos->get();
        
        // if($cliente && $cliente->pedidoMaisRecente){
        //     foreach ($planos as $key => &$plano) {
        //         // Log::info($plano->precos);
        //         $operador = '>=';
        //         if($plano->tipo == $cliente->pedidoMaisRecente->tipo_plano) $operador = '>';
        //         $precos = $plano->precos->where('quant_anuncios', $operador, intval($cliente->pedidoMaisRecente->quant_anuncios));
        //         $plano->precos = $precos;
        //         // Log::info($precos);
        //     }
        // }

        $listaPlanosDTO = DtoHelper::getListaPlanosDTO($planos);
        return $this->getResponse('success', [
            'listaPlanos' => $listaPlanosDTO,
        ]);
    }
}

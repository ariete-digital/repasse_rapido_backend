<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Modelo;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Modelo::where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->with('marca')
                ->paginate(MarcaController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Modelo::with('marca')->paginate(MarcaController::NUM_REG_POR_PAG);
        }
        $modelos = $paginacao->items();
        $listaMarcasDTO = DtoHelper::getListaModelosDTO($modelos);
        return $this->getResponse('success', [
            'listaMarcas' => $listaMarcasDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $modeloDTO = [];
        if ($request->id) {
            $modelo = Modelo::where('id', $request->id)->with('marca')->first();
            $modeloDTO = [
                'id' => $modelo->id,
                'descricao' => $modelo->descricao,
                'marca' => [
                    'id' => $modelo->marca->id,
                    'descricao' => $modelo->marca->descricao,
                ]
            ];
        } else {
            $modeloDTO = [
                'id' => 0,
                'descricao' => '',
                'marca' => []
            ];
        }
        return $this->getResponse('success', [
            'modelo' => $modeloDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'id_marca' => 'required',
        ]);

        $modelo = new Modelo();
        if ($request->id != null) {
            $modelo = Modelo::where('id', $request->id)->first();
            $modelo->descricao = $request->descricao;
            $modelo->id_marca = $request->id_marca;
            $modelo->save();
        } else {
            $modelo = Modelo::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                    'id_marca' => $request->id_marca,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Modelo salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $modelo = Modelo::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Modelo excluído com sucesso!"
        ]);
    }

    public function listagemModelos(Request $request){
        $modelos = Modelo::where('nome_curto', 'LIKE', $request->filtro.'%');
        if($request->id_marca){
            $modelos = $modelos->where('id_marca', $request->id_marca);
        }
        if($request->tipo_veiculo){
            $modelos = $modelos->whereRelation('marca', 'tipo_veiculo', '=', $request->tipo_veiculo);
        }
        $modelos = $modelos->selectRaw('MIN(id) as id, nome_curto')
            ->groupBy('nome_curto')
            ->orderBy('nome_curto')
            ->get();
        
        // Log::info($cidades);
        $modelosDTO = DtoHelper::getListagemModelosDTO($modelos);
        return $this->getResponse('success', $modelosDTO);
    }
}

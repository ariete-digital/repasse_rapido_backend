<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Marca::where('nome', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(MarcaController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Marca::paginate(MarcaController::NUM_REG_POR_PAG);
        }
        $marcas = $paginacao->items();
        $listaMarcasDTO = DtoHelper::getListaItemSimplesDTO($marcas);
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
        $marcaDTO = [];
        if ($request->id) {
            $marca = Marca::where('id', $request->id)->first();
            $marcaDTO = [
                'id' => $marca->id,
                'descricao' => $marca->descricao,
            ];
        } else {
            $marcaDTO = [
                'id' => 0,
                'descricao' => '',
            ];
        }
        return $this->getResponse('success', [
            'marca' => $marcaDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $cor = new Marca();
        if ($request->id != null) {
            $cor = Marca::where('id', $request->id)->first();
            $cor->descricao = $request->descricao;
            $cor->save();
        } else {
            $cor = Marca::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Marca salva com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $marca = Marca::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Marca excluída com sucesso!"
        ]);
    }

    public function listagemMarcas(Request $request){
        $marcas = Marca::where('descricao', 'LIKE', '%'.$request->filtro.'%');
        if($request->tipo_veiculo){
            $marcas = $marcas->where('tipo_veiculo', $request->tipo_veiculo);
        }
        $marcas = $marcas->orderBy('descricao')->get();
        // Log::info($cidades);
        $marcasDTO = DtoHelper::getListagemAsyncSelectDTO($marcas);
        return $this->getResponse('success', $marcasDTO);
    }
}

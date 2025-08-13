<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cor;
use Illuminate\Http\Request;

class CorController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Cor::where('descricao', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(CorController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Cor::paginate(CorController::NUM_REG_POR_PAG);
        }
        $cores = $paginacao->items();
        $listaCoresDTO = DtoHelper::getListaItemSimplesDTO($cores);
        return $this->getResponse('success', [
            'listaCores' => $listaCoresDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $corDTO = [];
        if ($request->id) {
            $cor = Cor::where('id', $request->id)->first();
            $corDTO = [
                'id' => $cor->id,
                'descricao' => $cor->descricao,
            ];
        } else {
            $corDTO = [
                'id' => 0,
                'descricao' => '',
            ];
        }
        return $this->getResponse('success', [
            'cor' => $corDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $cor = new Cor();
        if ($request->id != null) {
            $cor = Cor::where('id', $request->id)->first();
            $cor->descricao = $request->descricao;
            $cor->save();
        } else {
            $cor = Cor::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Cor salva com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $cor = Cor::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Cor excluída com sucesso!"
        ]);
    }

    public function listagemCores(Request $request){
        $cores = Cor::where('descricao', 'LIKE', '%'.$request->filtro.'%')
            ->orderBy('descricao')
            ->get();
        // Log::info($cidades);
        $coresDTO = DtoHelper::getListagemAsyncSelectDTO($cores);
        return $this->getResponse('success', $coresDTO);
    }
}

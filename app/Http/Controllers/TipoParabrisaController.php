<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\TipoParabrisa;
use Illuminate\Http\Request;

class TipoParabrisaController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = TipoParabrisa::where('descricao', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(TipoParabrisaController::NUM_REG_POR_PAG);
        } else {
            $paginacao = TipoParabrisa::paginate(TipoParabrisaController::NUM_REG_POR_PAG);
        }
        $tipos = $paginacao->items();
        $listaTiposDTO = DtoHelper::getListaItemSimplesDTO($tipos);
        return $this->getResponse('success', [
            'listaTiposParabrisa' => $listaTiposDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $tipoDTO = [];
        if ($request->id) {
            $tipo = TipoParabrisa::where('id', $request->id)->first();
            $tipoDTO = [
                'id' => $tipo->id,
                'descricao' => $tipo->descricao,
            ];
        } else {
            $tipoDTO = [
                'id' => 0,
                'descricao' => '',
            ];
        }
        return $this->getResponse('success', [
            'listaTiposParabrisa' => $tipoDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $tipo = new TipoParabrisa();
        if ($request->id != null) {
            $tipo = TipoParabrisa::where('id', $request->id)->first();
            $tipo->descricao = $request->descricao;
            $tipo->save();
        } else {
            $tipo = TipoParabrisa::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Tipo de parabrisa salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $tipo = TipoParabrisa::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Tipo de parabrisa excluído com sucesso!"
        ]);
    }
}

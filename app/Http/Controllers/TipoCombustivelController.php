<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\TipoCombustivel;
use Illuminate\Http\Request;

class TipoCombustivelController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = TipoCombustivel::where('descricao', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(TipoCombustivelController::NUM_REG_POR_PAG);
        } else {
            $paginacao = TipoCombustivel::paginate(TipoCombustivelController::NUM_REG_POR_PAG);
        }
        $tipos = $paginacao->items();
        $listaTiposDTO = DtoHelper::getListaItemSimplesDTO($tipos);
        return $this->getResponse('success', [
            'listaTiposCombustivel' => $listaTiposDTO,
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
            $tipo = TipoCombustivel::where('id', $request->id)->first();
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
            'tipoCombustivel' => $tipoDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $tipo = new TipoCombustivel();
        if ($request->id != null) {
            $tipo = TipoCombustivel::where('id', $request->id)->first();
            $tipo->descricao = $request->descricao;
            $tipo->save();
        } else {
            $tipo = TipoCombustivel::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Tipo de combustivel salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $tipo = TipoCombustivel::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Tipo de combustivel excluído com sucesso!"
        ]);
    }
}

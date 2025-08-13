<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\TipoPneu;
use Illuminate\Http\Request;

class TipoPneuController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = TipoPneu::where('descricao', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(TipoPneuController::NUM_REG_POR_PAG);
        } else {
            $paginacao = TipoPneu::paginate(TipoPneuController::NUM_REG_POR_PAG);
        }
        $tipos = $paginacao->items();
        $listaTiposDTO = DtoHelper::getListaItemSimplesDTO($tipos);
        return $this->getResponse('success', [
            'listaTiposPneu' => $listaTiposDTO,
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
            $tipo = TipoPneu::where('id', $request->id)->first();
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
            'tipoPneu' => $tipoDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $tipo = new TipoPneu();
        if ($request->id != null) {
            $tipo = TipoPneu::where('id', $request->id)->first();
            $tipo->descricao = $request->descricao;
            $tipo->save();
        } else {
            $tipo = TipoPneu::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Tipo de pneu salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $tipo = TipoPneu::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Tipo de pneu excluído com sucesso!"
        ]);
    }
}

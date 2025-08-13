<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\TipoCambio;
use Illuminate\Http\Request;

class TipoCambioController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = TipoCambio::where('descricao', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(TipoCambioController::NUM_REG_POR_PAG);
        } else {
            $paginacao = TipoCambio::paginate(TipoCambioController::NUM_REG_POR_PAG);
        }
        $tipos = $paginacao->items();
        $listaTiposDTO = DtoHelper::getListaItemSimplesDTO($tipos);
        return $this->getResponse('success', [
            'listaTiposCambio' => $listaTiposDTO,
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
            $tipo = TipoCambio::where('id', $request->id)->first();
            $tipoDTO = [
                'id' => $tipo->id,
                'descricao' => $tipo->descricao,
                'tipo_veiculo' => $tipo->tipo_veiculo,
            ];
        } else {
            $tipoDTO = [
                'id' => 0,
                'descricao' => '',
                'tipo_veiculo' => '',
            ];
        }
        return $this->getResponse('success', [
            'tipoCambio' => $tipoDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
        ]);

        $tipoCambio = new TipoCambio();
        if ($request->id != null) {
            $tipoCambio = TipoCambio::where('id', $request->id)->first();
            $tipoCambio->descricao = $request->descricao;
            $tipoCambio->tipo_veiculo = $request->tipo_veiculo;
            $tipoCambio->save();
        } else {
            $tipoCambio = TipoCambio::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'descricao' => $request->descricao,
                    'tipo_veiculo' => $request->tipo_veiculo,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Tipo de cambio salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $tipoCambio = TipoCambio::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Tipo de cambio excluído com sucesso!"
        ]);
    }
}

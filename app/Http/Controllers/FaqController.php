<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function obterFaqCompleto(Request $request)
    {
        $faq = Faq::all();
        $listaFaqDTO = DtoHelper::getListaFaqDTO($faq);
        return $this->getResponse('success', [
            'faq' => $listaFaqDTO,
        ]);
    }
    
    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = Faq::where('pergunta', 'LIKE', '%' . $request->filtro . '%')
                ->paginate(FaqController::NUM_REG_POR_PAG);
        } else {
            $paginacao = Faq::paginate(FaqController::NUM_REG_POR_PAG);
        }
        $faq = $paginacao->items();
        $listaFaqDTO = DtoHelper::getListaFaqDTO($faq);
        return $this->getResponse('success', [
            'faq' => $listaFaqDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $faqDTO = [];
        if ($request->id) {
            $faq = Faq::where('id', $request->id)->first();
            $faqDTO = [
                'id' => $faq->id,
                'pergunta' => $faq->pergunta,
                'resposta' => $faq->resposta,
            ];
        } else {
            $faqDTO = [
                'id' => 0,
                'pergunta' => '',
                'resposta' => '',
            ];
        }
        return $this->getResponse('success', [
            'faq' => $faqDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'pergunta' => 'required|string|max:255',
            'resposta' => 'required|string',
        ]);

        $faq = new Faq();
        if ($request->id != null) {
            $faq = Faq::where('id', $request->id)->first();
            $faq->pergunta = $request->pergunta;
            $faq->resposta = $request->resposta;
            $faq->save();
        } else {
            $faq = Faq::firstOrCreate(
                [
                    'id' => null,
                ],
                [
                    'pergunta' => $request->pergunta,
                    'resposta' => $request->resposta,
                ]
            );
        }

        return $this->getResponse('success', [
            'message' => "Pergunta/resposta do FAQ salva com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $faq = Faq::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Pergunta/resposta do FAQ excluída com sucesso!"
        ]);
    }
}

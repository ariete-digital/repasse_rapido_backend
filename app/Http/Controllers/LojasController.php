<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cidade;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LojasController extends Controller
{
    public function lista(Request $request)
    {
        $lojas = Cliente::where('tipo', 'PJ')
            ->whereRelation('usuario', 'active', '=', true);
        if ($request->id_estado) {
            $lojas = $lojas->whereRelation('cidade.estado', 'id', '=', $request->id_estado);
        }
        if($request->id_cidade){
            $lojas = $lojas->where('id_cidade', $request->id_cidade);
        }
        $lojas = $lojas->get();
        $listaLojasDTO = DtoHelper::getListaLojasDTO($lojas);
        return $this->getResponse('success', [
            'lojas' => $listaLojasDTO,
        ]);
    }

    public function show(Request $request, string $id)
    {
        $loja = Cliente::where([
            'tipo' => 'PJ',
            'id' => $id
        ])->first();

        if (!$loja) {
            return $this->getResponse('error', 'Loja não encontrada', 404);
        }

        $dto = DtoHelper::getLojaDTO($loja);
        return $this->getResponse('success', $dto);
    }

    public function get(Request $request)
    {
        $loja = Cliente::where([
            'tipo' => 'PJ',
            'slug' => $request->slug
        ])->first();

        if (!$loja) {
            return $this->getResponse('error', 'Loja não encontrada', 404);
        }

        $dto = DtoHelper::getLojaDTO($loja);
        return $this->getResponse('success', $dto);
    }

    public function getLogos(Request $request)
    {
        Log::info($request->all());
        $cidadeAtual = Cidade::whereRelation('estado', 'nome', 'like', '%'.$request->state.'%')->where('nome', $request->city)->first();

        $lojas = Cliente::where('tipo', 'PJ')->whereNotNull('imagem_logo')
            ->whereRelation('usuario', 'active', '=', true);

        if($cidadeAtual) $lojas->where('id_cidade', $cidadeAtual->id);
        $lojas = $lojas->inRandomOrder()->limit(7)->get();

        if(!$lojas || count($lojas) == 0){
            $lojas = Cliente::where('tipo', 'PJ')->whereNotNull('imagem_logo')->whereRelation('usuario', 'active', '=', true)->inRandomOrder()->limit(7)->get();
        }

        Log::info($lojas);
        $listaLojasDTO = DtoHelper::getListaLojasDTO($lojas);
        return $this->getResponse('success', [
            'lojas' => $listaLojasDTO,
        ]);
    }
}

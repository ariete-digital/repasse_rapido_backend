<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\ParametrosGerais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracoesController extends Controller
{
    public function obter(Request $request)
    {
        $parametros = ParametrosGerais::all();

        $parametrosDTO = DtoHelper::getParametrosGeraisDTO($parametros);

        return $this->getResponse('success', [
            'parametros' => $parametrosDTO,
        ]);
    }

    public function salvar(Request $request)
    {
        foreach ($request->all() as $chave => $valor) {
            if($chave != 'q'){
                ParametrosGerais::updateOrCreate(
                    ['chave' => $chave],
                    ['valor' => $valor]
                );
            }
        }

        return $this->getResponse('success', [
            'message' => 'Configurações salvas com sucesso!'
        ]);
    }
}

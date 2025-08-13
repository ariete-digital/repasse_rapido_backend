<?php

namespace App\Http\Controllers;

use App\Helpers\AnuncioHelper;
use App\Helpers\DtoHelper;
use App\Models\Anuncio;
use App\Models\Cidade;
use App\Services\AnunciosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    const TOTAL_LIMIT_ANUNCIOS = 12;

    public function __construct(private readonly AnunciosService $anunciosService)
    {
    }

    public function obterInfo(Request $request)
    {
        $cidadeAtual = Cidade::whereRelation('estado', 'nome', 'like', '%'.$request->state.'%')->where('nome', $request->city)->first();
        // Log::info(json_encode([
        //     'nomeCidade' => $request->city,
        //     'nomeEstado' => $request->state,
        //     'cidadeAtual' => $cidadeAtual,
        // ]));

        $anunciosDestaque = AnuncioHelper::getAnuncioBaseQuery()
            ->where('tipo_plano', 'D');
        if($cidadeAtual) $anunciosDestaque->whereRelation('cliente', 'id_cidade', '=', $cidadeAtual->id);
        $anunciosDestaque = $anunciosDestaque->inRandomOrder()->limit(HomeController::TOTAL_LIMIT_ANUNCIOS)->get();

        if(!$anunciosDestaque || count($anunciosDestaque) == 0){
            $anunciosDestaque = Anuncio::where('tipo_plano', 'D')->inRandomOrder()->limit(HomeController::TOTAL_LIMIT_ANUNCIOS)->get();
        }

        $anunciosDestaqueDTO = DtoHelper::getListaAnunciosDTO($anunciosDestaque, false, true, true);
        // $anunciosNormaisDTO = DtoHelper::getListaAnunciosDTO($anunciosNormais, false, true, true);
        // $outrosAnunciosDTO = DtoHelper::getListaAnunciosDTO($outrosAnuncios, false, true, true);

        return $this->getResponse('success', [
            'anunciosDestaque' => $anunciosDestaqueDTO,
            // 'anunciosNormais' => $anunciosNormaisDTO,
            // 'outrosAnuncios' => $outrosAnunciosDTO,
        ]);
    }
}

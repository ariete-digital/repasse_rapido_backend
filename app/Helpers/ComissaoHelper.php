<?php

namespace App\Helpers;

use App\Models\ComissaoUsuario;
use App\Models\EscritorioRegional;
use App\Models\ParametrosGerais;
use App\Models\PrecoAnuncioPlano;
use App\Models\Subregiao;
use Illuminate\Support\Facades\Auth;

class ComissaoHelper
{
    public static function salvarComissaoVenda($idPedido, $idEstado, $cep, $valorTotal)
    {
        try {
            $escritorioRegional = EscritorioRegional::whereRelation('ufs', 'ufs.id', '=', $idEstado)
            ->first();

            $subregiao = Subregiao::whereRelation('faixasCep', 'cep_inicial', '<=', intval(str_replace('.', '', str_replace('-', '', $cep))))
                ->whereRelation('faixasCep', 'cep_final', '>', intval(str_replace('.', '', str_replace('-', '', $cep))))
                ->first();
            if(!$subregiao){
                $subregiao = Subregiao::whereRelation('ufs', 'ufs.id', '=', $idEstado)
                    ->first();
            }

            if($escritorioRegional && $escritorioRegional->id_usuario){
                $comissaoEscritorio = ComissaoUsuario::create([
                    'id_usuario' => $escritorioRegional->id_usuario,
                    'id_pedido' => $idPedido,
                    'percentual' => $escritorioRegional->percentual_comissao,
                    'valor' => floatval($escritorioRegional->percentual_comissao) / 100 * $valorTotal,
                ]);
            }

            if($subregiao && $subregiao->id_usuario){
                $comissaoSubregiao = ComissaoUsuario::create([
                    'id_usuario' => $subregiao->id_usuario,
                    'id_pedido' => $idPedido,
                    'percentual' => $subregiao->percentual_comissao,
                    'valor' => floatval($subregiao->percentual_comissao) / 100 * $valorTotal,
                ]);
            }
        } catch (\Throwable $th) {
            Log::info(''. $th->getMessage());
        }
        
    }

    public static function salvarComissaoModeracao($idAnuncio, $tipoPlano)
    {
        $precoPlano = PrecoAnuncioPlano::whereRelation('plano', 'tipo', '=', $tipoPlano)->where('quant_anuncios', 1)->first();
        $percentual = floatval(Auth::user()->percentual_comissao);
        $comissaoModerador = ComissaoUsuario::create([
            'id_usuario' => Auth::id(),
            'id_anuncio' => $idAnuncio,
            'percentual' => $percentual,
            'valor' => floatval($percentual) / 100 * floatval($precoPlano->preco),
        ]);
    }
}

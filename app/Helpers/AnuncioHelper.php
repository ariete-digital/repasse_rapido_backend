<?php

namespace App\Helpers;

use App\Models\Anuncio;
use App\Models\AnuncioRascunho;
use App\Models\Cliente;
use App\Models\ImagensAnuncio;
use App\Models\ImagensRascunho;
use App\Models\LicencaAnuncio;
use App\Models\OpcionaisAnuncio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnuncioHelper
{
    public static function gerarAnuncioDoRascunho($anuncioRascunho, $idAnuncioOriginal = null)
    {
        $anuncioRascunhoArray = $anuncioRascunho->toArray();
        unset($anuncioRascunhoArray['opcionais']);
        unset($anuncioRascunhoArray['imagens']);
        
        $updateArray = [
            ...$anuncioRascunhoArray
        ];
        if(!$idAnuncioOriginal){
            $updateArray['codigo'] = Anuncio::geraCodigo();
        }

        $anuncio = Anuncio::updateOrCreate(
            [
                'id' => $idAnuncioOriginal
            ],
            $updateArray
        );
        foreach ($anuncioRascunho->opcionais as $key => $opcional) {
            $opcional = OpcionaisAnuncio::create([
                'id_opcional' => $opcional->id,
                'id_anuncio' => $anuncio->id,
            ]);
        }

        ImagensAnuncio::where('id_anuncio', $anuncio->id)->delete();

        $oldPath = ImagensRascunho::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncioRascunho->id;
        $newPath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
        foreach ($anuncioRascunho->imagens as $key => $imagem) {
            $imagem = ImagensAnuncio::create([
                'id_anuncio' => $anuncio->id,
                'arquivo' => $imagem->arquivo,
                'principal'=> $imagem->principal,
            ]);
            $oldPathFile = $oldPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            $newPathFile = $newPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            Storage::copy($oldPathFile,$newPathFile);
        }
        Storage::deleteDirectory($oldPath);
        $anuncioRascunho->delete();

        return $anuncio;
    }

    public static function obterImagensRascunho($anuncioRascunho)
    {
        $imagens = [];
        if($anuncioRascunho){
            $basePath = ImagensRascunho::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncioRascunho->id;
            foreach ($anuncioRascunho->imagens as $key => $imagem) {
                if(Config::get('app.env') == 'production'){
                    $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $imagem->arquivo;
                } else {
                    $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                }
                $array = [
                    'id' => $imagem->id,
                    'arquivo' => $imagem->arquivo,
                    'str_base64' => $urlImg,
                    'principal' => $imagem->principal,
                ];
    
                array_push($imagens, $array);
                // $file = Storage::get($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                // Log::info(json_encode([
                //     'basePath' => $basePath,
                //     'base64' => $base64,
                //     // 'file' => $file,
                //     'arquivo' => $imagem->arquivo,
                // ]));
            }
        }

        return $imagens;
    }

    public static function criarRascunhoRepasse($id_cliente)
    {
        $anuncioRascunho = AnuncioRascunho::create([
            'tipo_venda'=> 'R',
            'tipo_vendedor'=> 'R',
            'tipo_plano'=> 'A',
            'id_cliente'=> $id_cliente,
        ]);

        return $anuncioRascunho->id;
    }

    public static function enviarRelatorioCliques()
    {
        $cabecalho = "*Relatório diário de cliques por anúncio*\n\n";
        $clientes = Cliente::with(['anuncios' => function($query){
            $query->where('pausado', false)
                ->where('ativo', true)
                ->with('modelo');
        }])->get();
        $corpo = "";
        foreach ($clientes as $key => $cliente) {
            if($cliente->anuncios->count()){
                $corpo = $corpo . "Anuncios ativos: *" . $cliente->anuncios->count() . "*\n\n";
                $corpo = $corpo . "*Codigo - Veiculo - Cliques* \n";
                foreach ($cliente->anuncios as $key => $anuncio) {
                    $corpo = $corpo . $anuncio->codigo . " - " . $anuncio->modelo->descricao . " - (" . $anuncio->num_cliques . ");\n";
                }
                $msg = $cabecalho . $corpo;
                WhatsappHelper::enviarMensagem($cliente->celular, $msg, $cliente->id_usuario);
                // Log::info($msg);
                $corpo = "";
            }
        }
    }

    public static function processaLicencasVencidas()
    {
        Log::info('Processando licenças vencidas');
        $licencas = LicencaAnuncio::all();
        foreach ($licencas as $key => $licenca) {
            if($licenca->is_vencida){
                $anuncios = Anuncio::where('id_cliente', $licenca->id_cliente)->where('ativo', true)->where('pausado', false)->get();
                foreach ($anuncios as $key => $anuncio) {
                    $anuncio->pausado = true;
                    $anuncio->save();
                }
            }
        }
    }

    public static function processaAnunciosVencidos()
    {
        Log::info('Processando anúncios vencidos');
        $anuncios = Anuncio::where('ativo', true)->where('pausado', false)->get();
        foreach ($anuncios as $key => $anuncio) {
            if($anuncio->is_vencido){
                $anuncio->pausado = true;
                $anuncio->save();
            }
        }
    }

    public static function getAnuncioBaseQuery()
    {
        return Anuncio::where('ativo', true)
            ->where('pausado', false)
            ->whereRelation('cliente.usuario', 'active', '=', true);
    }
}

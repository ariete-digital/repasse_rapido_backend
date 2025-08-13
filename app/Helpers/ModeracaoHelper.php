<?php

namespace App\Helpers;

use App\Mail\ReprovacaoModeracaoEmail;
use App\Models\AnuncioRascunho;
use App\Models\Cliente;
use App\Models\HistoricoModeracaoAnuncio;
use App\Models\ImagensAnuncio;
use App\Models\ImagensHistoricoModeracao;
use App\Models\ImagensRascunho;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ModeracaoHelper
{
    public static function criarHistoricoModeracao($anuncio)
    {
        $historico = HistoricoModeracaoAnuncio::create([
            'id_anuncio' => $anuncio->id,
            'codigo_anuncio' => $anuncio->codigo,
            'email_usuario_moderacao' => Auth::user()->email,
            'nome_usuario_moderacao' => Auth::user()->nome,
            'perfil_usuario_moderacao' => Auth::user()->role,
            'cnh_anunciante' => '',
            'comprovante_anunciante' => '',
            'doc_complementar_anunciante' => '',
            'obs_moderacao' => $anuncio->obs_moderacao,
        ]);

        $imagens = ImagensAnuncio::where('id_anuncio', $anuncio->id)->get();

        $oldPath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
        $newPath = HistoricoModeracaoAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $historico->id;
        foreach ($imagens as $key => $imagem) {
            $imagemHistorico = ImagensHistoricoModeracao::create([
                'id_historico' => $historico->id,
                'nome_arquivo' => $imagem->arquivo,
            ]);
            $oldPathFile = $oldPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            $newPathFile = $newPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            Storage::copy($oldPathFile,$newPathFile);
        }

        $cliente = Cliente::where('id', $anuncio->id_cliente)->first();
        $basePathCliente = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $cliente->id;
        
        $oldPathCnh = $basePathCliente . DIRECTORY_SEPARATOR . $cliente->imagem_cnh;
        $newPathCnh = $newPath . DIRECTORY_SEPARATOR . $cliente->imagem_cnh;
        Storage::copy($oldPathCnh,$newPathCnh);
        $historico->cnh_anunciante = $cliente->imagem_cnh;

        $oldPathComprovante = $basePathCliente . DIRECTORY_SEPARATOR . $cliente->imagem_comprovante;
        $newPathComprovante = $newPath . DIRECTORY_SEPARATOR . $cliente->imagem_comprovante;
        Storage::copy($oldPathComprovante,$newPathComprovante);
        $historico->comprovante_anunciante = $cliente->imagem_comprovante;
        
        $oldPathDocComplementar = $basePathCliente . DIRECTORY_SEPARATOR . $cliente->imagem_doc_complementar;
        $newPathDocComplementar = $newPath . DIRECTORY_SEPARATOR . $cliente->imagem_doc_complementar;
        Storage::copy($oldPathDocComplementar,$newPathDocComplementar);
        $historico->doc_complementar_anunciante = $cliente->imagem_doc_complementar;

        $historico->save();

        return $historico;
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

    public static function enviarEmailModeracao($email, $nome, $obs)
    {
        try {
            $mailData = [
                'nome' => $nome,
                'obs' => $obs,
                'mailFrom' => env('MAIL_FROM_ADDRESS'),
                'nameFrom' => env('MAIL_FROM_NAME'),
            ];
            Mail::to($email)->send(new ReprovacaoModeracaoEmail($mailData));
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}

<?php

namespace App\Helpers;

use App\Models\EnvioWhatsapp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class WhatsappHelper
{
    public static function enviarMensagem($numero, $mensagem, $idUsuario)
    {
        $envioWpp = EnvioWhatsapp::create([
            'id_usuario' => $idUsuario,
            'numero' => $numero,
            'mensagem' => $mensagem,
        ]);
        
        $id = Config::get('zapi.id_instance');
        $tokenInstance = Config::get('zapi.token_instance');
        $tokenAccount = Config::get('zapi.token_account');
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.z-api.io/instances/".$id."/token/".$tokenInstance."/send-text",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"phone\": \"".$numero."\", \"message\": \"".$mensagem."\"}",
            CURLOPT_HTTPHEADER => array(
                "client-token: ".$tokenAccount,
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::error("cURL Error #:" . $err);
        } else {
            //salvar no banco
            $resp = json_decode($response, true);

            if(isset($resp['zaapId']) && isset($resp['messageId'])){
                $envioWpp->zaapId = $resp['zaapId'];
                $envioWpp->messageId = $resp['messageId'];
                $envioWpp->data_envio = Carbon::now();
                $envioWpp->save();
            }
        }
        return true;
    }
}

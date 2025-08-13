<?php

namespace App\Helpers;

use App\Models\EnvioWhatsapp;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ApiPlacasHelper
{
    public static function buscarInfoVeiculo($placa)
    {
        $urlBase = Config::get('apiplacas.url_base');
        $token = Config::get('apiplacas.token');
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlBase.$placa."/".$token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            Log::error("cURL Error #:" . $err);
            throw new Exception($err);
        } else {
            $resp = json_decode($response, true);
            return $resp;
        }
    }
}

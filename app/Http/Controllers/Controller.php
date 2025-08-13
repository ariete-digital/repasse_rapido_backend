<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getResponse($status, $content, $httpCode = 200){
        // var_dump($content);exit;
        return response()->json([
            'status' => $status,
            'content' => $content
        ], $httpCode);
    }

    protected function getResponseNoContent(){
        return response('', 204);
    }
}

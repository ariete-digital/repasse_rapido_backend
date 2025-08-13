<?php

namespace App\Http\Controllers;

use App\Mail\EnvioContatoEmail;
use App\Mail\EnvioNotificacaoRepasseEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContatoController extends Controller
{
    public function enviarContato(Request $request)
    {
        try {
            $data = [
                'nome' => $request->nome,
                'email' => $request->email,
                'telefone' => $request->telefone,
                'assunto' => $request->assunto,
                'mensagem' => $request->mensagem,
                'mailFrom' => env('MAIL_FROM_ADDRESS'),
                'nameFrom' => env('MAIL_FROM_NAME'),
            ];
            Mail::to('contato@queroauto.com.br')->send(new EnvioContatoEmail($data));
        } catch (\Throwable $th) {
            Log::error($th);
        }

        return $this->getResponse('success', [
            'message' => "Mensagem enviada com sucesso!"
        ]);
    }

    public function enviarNotificacaoRepasse(Request $request)
    {
        try {
            $data = [
                'nome' => $request->nome,
                'telefone' => $request->telefone,
                'estado' => $request->estado,
                'mailFrom' => env('MAIL_FROM_ADDRESS'),
                'nameFrom' => env('MAIL_FROM_NAME'),
            ];
            Mail::to('repasses@queroauto.com.br')->send(new EnvioNotificacaoRepasseEmail($data));
        } catch (\Throwable $th) {
            Log::error($th);
        }

        return $this->getResponse('success', [
            'message' => "Mensagem enviada com sucesso!"
        ]);
    }
}

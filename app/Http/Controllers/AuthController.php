<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmacaoContaEmail;
use App\Mail\ConfirmacaoTrocaSenhaEmail;
use App\Mail\RecuperacaoSenhaEmail;
use App\Models\Cliente;
use App\Models\ConfirmacaoCadastro;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Log::info($request->all());

        if($request->token){
            $confirmacao = ConfirmacaoCadastro::whereRelation('cliente.usuario', 'email', '=', $request->email)->first();
            if($confirmacao->codigo == $request->token){
                $usuario = User::where('email', $request->email)->first();
                $usuario->active = true;
                $usuario->save();
                $confirmacao->delete();
            }
        }

        $conditions = array_merge($request->only('email', 'password'), ['active' => true]);
        $token = Auth::attempt($conditions);
        if (!$token) {
            return $this->getResponse('error', [
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        $userDTO = [
            'id' => $user->id,
            'nome' => $user->nome,
            'email' => $user->email,
            'role' => $user->role,
            'access_token' => $token,
        ];

        $cliente = Cliente::where('id_usuario', $user->id)->first();
        if($cliente){
            $userDTO['tipo'] = $cliente->tipo;
        }

        return $this->getResponse('success', [
            'user' => $userDTO
        ]);
    }

    public function cadastrar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'senha' => 'required'
        ]);

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->senha),
            'role' => 'cliente',
            'active' => false
        ]);

        $clienteArray = [
            'id_usuario' => $user->id,
            'tipo' => $request->tipo,
            'num_documento' => $request->num_documento,
        ];
        if($request->tipo == 'PJ'){
            $clienteArray = [
                ...$clienteArray,
                'nome_fantasia'=> $request->nome_fantasia,
                'slug'=> Str::slug($request->nome_fantasia, '-'),
                'celular'=> $request->celular,
                'telefone'=> $request->telefone,
                'cep'=> $request->cep,
                'logradouro'=> $request->logradouro,
                'bairro'=> $request->bairro,
                'numero'=> $request->numero,
                'complemento'=> $request->complemento,
                'nome_responsavel'=> $request->nome_responsavel,
                'cpf_responsavel'=> $request->cpf_responsavel,
                'id_cidade'=> $request->id_cidade,
                'inscricao_estadual'=> $request->inscricao_estadual,
            ];
        }

        $cliente = Cliente::create($clienteArray);

        $basePath = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $cliente->id;
        if($request->comprovEnd){
            $file = $request->comprovEnd;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_comprovante = $file->getClientOriginalName();
        }
        if($request->cnh){
            $file = $request->cnh;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_cnh = $file->getClientOriginalName();
        }

        $cliente->save();

        $token = Str::random(64);
        ConfirmacaoCadastro::updateOrCreate(
            ['id_cliente' => $cliente->id],
            ['codigo' => $token]
        );

        // $token = Auth::login($user);
        // $userDTO = [
        //     'id' => $user->id,
        //     'nome' => $user->nome,
        //     'email' => $user->email,
        //     'role' => $user->role,
        //     'access_token' => $token,
        //     'tipo' => $cliente->tipo,
        // ];

        try {
            $mailData = [
                'nome' => $user->nome,
                'dataHora' => Carbon::now()->format('d/m/Y H:i'),
                'url' => Config::get('app.url_frontend') . "/login?token=".$token
            ];
            Mail::to($request->email)->send(new ConfirmacaoContaEmail($mailData));
        } catch (\Throwable $th) {
            Log::error($th);
        }

        return $this->getResponse('success', []);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        try {
            $newToken = Auth::refresh();
            return $this->getResponse('success', [
                'access_token' => $newToken,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->getResponse('invalid', []);
        }
    }

    public function recuperarSenha(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(64);

            PasswordReset::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token]
            );

            try {
                $data = [
                    'nome' => $user->nome,
                    'dataHora' => Carbon::now()->format('d/m/Y H:i'),
                    'token' => $token,
                    'mailFrom' => env('MAIL_FROM_ADDRESS'),
                    'nameFrom' => env('MAIL_FROM_NAME'),
                    'url' => $request->url . $token,
                ];
                Mail::to($request->email)->send(new RecuperacaoSenhaEmail($data));
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }

        return $this->getResponse('success', [
            'message' => 'E-mail enviado com sucesso!',
        ]);
    }

    public function cadastrarNovaSenha(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'senha' => 'required',
        ]);

        $passwordReset = PasswordReset::where([
            'token' => $request->token
        ])->first();

        if (!$passwordReset) {
            return $this->getResponse('success', [
                'message' => 'Não foi possível recuperar a senha.',
            ]);
        }

        $user = User::where('email', $passwordReset->email)->first();
        $user->password = Hash::make($request->senha);
        $user->save();

        PasswordReset::where(['email' => $passwordReset->email])->delete();

        try {
            $mailData = [
                'nome' => $user->name,
                'dataHora' => Carbon::now()->format('d/m/Y H:i'),
                'mailFrom' => env('MAIL_FROM_ADDRESS'),
                'nameFrom' => env('MAIL_FROM_NAME'),
            ];
            Mail::to($user->email)->send(new ConfirmacaoTrocaSenhaEmail($mailData));
        } catch (\Throwable $th) {
            Log::error($th);
        }

        return $this->getResponse('success', [
            'message' => 'Senha alterada com sucesso. Faça login para prosseguir.',
        ]);
    }
}

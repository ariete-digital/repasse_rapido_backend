<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        $paginacao = User::where('role', '<>', 'cliente');
        if ($request->filtro) {
            $paginacao = $paginacao->where(function ($query) use ($request){
                    $query->where('nome', 'LIKE', '%' . $request->filtro . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->filtro . '%');
                })->paginate(UsuarioController::NUM_REG_POR_PAG);
        } else {
            $paginacao = $paginacao->paginate(UsuarioController::NUM_REG_POR_PAG);
        }
        $usuarios = $paginacao->items();
        $listaUsuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios);
        return $this->getResponse('success', [
            'listaUsuarios' => $listaUsuariosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $usuarioDTO = [];
        if ($request->id) {
            $usuario = User::where('id', $request->id)->first();
            $usuarioDTO = DtoHelper::getUsuarioDTO($usuario);
        } else {
            $usuarioDTO = [
                'id' => 0,
                'nome' => '',
                'email' => '',
                'active' => '',
                'role' => '',
                'endereco' => '',
                'telefone' => '',
                'nome_banco' => '',
                'num_agencia' => '',
                'num_conta' => '',
                'inscricao_estadual' => '',
                'cnpj' => '',
                'percentual_comissao' => '',
            ];
        }
        return $this->getResponse('success', [
            'usuario' => $usuarioDTO,
            'perfis' => $this->getPerfisByRole(Auth::user()->role)
        ]);
    }

    public function salvar(Request $request)
    {
        // Log::info(json_encode([
        //     'request' => $request->all()
        // ]));
        $mailValidation = 'required|string|email|max:255';
        if ($request->id == null) {
            $mailValidation .= '|unique:users';
        }
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => $mailValidation,
        ]);

        $usuario = new User();
        $senhaPreenchida = $request->senha != '';
        if ($request->id != null) {
            $usuario = User::where('id', $request->id)->first();
            $usuario->nome = $request->nome;
            $usuario->email = $request->email;
            $usuario->role = $request->role;
            if ($senhaPreenchida) {
                $usuario->password = Hash::make($request->senha);
            }
            $usuario->active = $request->active === true;
            if($usuario->role != User::CODIGO_CLIENTE){
                $usuario->endereco = $request->endereco;
                $usuario->telefone = $request->telefone;
                $usuario->nome_banco = $request->nome_banco;
                $usuario->num_agencia = $request->num_agencia;
                $usuario->num_conta = $request->num_conta;
                $usuario->percentual_comissao = $request->percentual_comissao;
                $usuario->inscricao_estadual = $request->inscricao_estadual;
                $usuario->cnpj = $request->cnpj;
            }
            $usuario->save();
        } else {
            $usuario = User::firstOrCreate(
                [
                    'email' => $request->email,
                ],
                [
                    'nome' => $request->nome,
                    'endereco' => $request->endereco,
                    'telefone' => $request->telefone,
                    'nome_banco' => $request->nome_banco,
                    'num_agencia' => $request->num_agencia,
                    'num_conta' => $request->num_conta,
                    'inscricao_estadual' => $request->inscricao_estadual,
                    'cnpj' => $request->cnpj,
                    'percentual_comissao' => $request->percentual_comissao,
                    'password' => Hash::make($request->senha),
                    'role' => $request->role,
                    'active' => $request->active
                ]
            );
        }

        if($usuario->role == User::CODIGO_CLIENTE){
            $cliente = Cliente::updateOrCreate(
                [
                    'id_usuario' => $usuario->id,
                ],
                []
            );
        }

        return $this->getResponse('success', [
            'message' => "Usuário salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $usuario = User::where('id', $request->id)->first();
        $usuario->delete();
        return $this->getResponse('success', [
            'message' => "Usuário excluído com sucesso!"
        ]);
    }

    public function alterarStatus(Request $request)
    {
        $usuario = User::where('id', $request->id)->first();
        $usuario->active = $request->active;
        $usuario->save();

        $palavra = $request->active ? 'ativado' : 'desativado';
        return $this->getResponse('success', [
            'message' => "Usuário $palavra com sucesso!"
        ]);
    }

    public function listagemPerfis(Request $request)
    {
        return $this->getResponse('success', [
            'perfis' => $this->getPerfisByRole(Auth::user()->role)
        ]);
    }

    private function getPerfisByRole($role)
    {
        if($role == User::CODIGO_SUPERADMIN){
            return [
                [
                    'codigo' => User::CODIGO_ADMIN,
                    'nome' => 'Gerente Nacional',
                ],
                [
                    'codigo' => User::CODIGO_GERENTE,
                    'nome' => 'Gerente Regional',
                ],
                [
                    'codigo' => User::CODIGO_VENDEDOR,
                    'nome' => 'Representante',
                ],
                [
                    'codigo' => User::CODIGO_FINANCEIRO,
                    'nome' => 'Financeiro',
                ],
            ];
        } else if($role == User::CODIGO_ADMIN){
            return [
                [
                    'codigo' => User::CODIGO_GERENTE,
                    'nome' => 'Gerente Regional',
                ],
                [
                    'codigo' => User::CODIGO_VENDEDOR,
                    'nome' => 'Representante',
                ],
                [
                    'codigo' => User::CODIGO_FINANCEIRO,
                    'nome' => 'Financeiro',
                ],
                [
                    'codigo' => User::CODIGO_MODERADOR,
                    'nome' => 'Moderador',
                ],
            ];
        }
    }
}

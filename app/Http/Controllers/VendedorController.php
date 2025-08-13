<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\GerenteVendedor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VendedorController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        if ($request->filtro) {
            $paginacao = User::where(function ($query) use ($request) {
                    $query->where('nome', 'LIKE', '%' . $request->filtro . '%')
                        ->orWhere('email', 'LIKE', '%' . $request->filtro . '%');
                })
                ->where('role', User::CODIGO_VENDEDOR)
                ->whereRelation('gerente', 'id_gerente', '=', Auth::id())
                ->paginate(VendedorController::NUM_REG_POR_PAG);
        } else {
            $paginacao = User::where('role', User::CODIGO_VENDEDOR)
                ->whereRelation('gerente', 'id_gerente', '=', Auth::id())
                ->paginate(VendedorController::NUM_REG_POR_PAG);
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
            $usuarioDTO = [
                'id' => $usuario->id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'active' => $usuario->active == 1,
            ];
        } else {
            $usuarioDTO = [
                'id' => 0,
                'nome' => '',
                'email' => '',
                'active' => '',
            ];
        }
        return $this->getResponse('success', [
            'usuario' => $usuarioDTO,
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
            if ($senhaPreenchida) {
                $usuario->password = Hash::make($request->senha);
            }
            $usuario->active = $request->active === true;
            $usuario->save();
        } else {
            $usuario = User::firstOrCreate(
                [
                    'email' => $request->email,
                ],
                [
                    'nome' => $request->nome,
                    'password' => Hash::make($request->senha),
                    'role' => User::CODIGO_VENDEDOR,
                    'active' => $request->active
                ]
            );
        }

        GerenteVendedor::firstOrCreate(
            [
                'id_gerente' => Auth::id(),
                'id_vendedor' => $usuario->id
            ],
            []
        );

        return $this->getResponse('success', [
            'message' => "Vendedor salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $usuario = User::where('id', $request->id)->first();
        $usuario->delete();
        return $this->getResponse('success', [
            'message' => "Vendedor excluído com sucesso!"
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
}

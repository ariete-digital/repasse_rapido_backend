<?php

namespace App\Helpers;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    const AUTH_MAP = [
        [
            'path' => 'admin/usuarios',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/listagem',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/escritorios',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/subregioes',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/cores',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/tipos_cambio',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/tipos_combustivel',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/tipos_parabrisa',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/tipos_pneu',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/marcas',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/modelos',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/planos',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/faq',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/pedidos',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/usuarios_comissoes',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/comissoes',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        // [
        //     'path' => 'admin/minhas_comissoes',
        //     'roles' => [User::CODIGO_GERENTE, User::CODIGO_VENDEDOR, User::CODIGO_MODERADOR]
        // ],
        [
            'path' => 'admin/anuncios',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN, User::CODIGO_GERENTE]
        ],
        [
            'path' => 'admin/configuracoes',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/meus_clientes',
            'roles' => [User::CODIGO_VENDEDOR]
        ],
        [
            'path' => 'admin/vendedores',
            'roles' => [User::CODIGO_GERENTE]
        ],
        [
            'path' => 'admin/meu_escritorio',
            'roles' => [User::CODIGO_GERENTE]
        ],
        [
            'path' => 'admin/escritorios/obter_detalhes',
            'roles' => [User::CODIGO_GERENTE]
        ],
        [
            'path' => 'admin/subregioes/obter_detalhes',
            'roles' => [User::CODIGO_GERENTE]
        ],
        // [
        //     'path' => 'admin/moderacao',
        //     'roles' => [User::CODIGO_MODERADOR]
        // ],
        [
            'path' => 'admin/clientes',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN]
        ],
        [
            'path' => 'admin/clientes/obter_detalhes',
            'roles' => [User::CODIGO_GERENTE]
        ],
        [
            'path' => 'admin/minha_conta',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN, User::CODIGO_GERENTE, User::CODIGO_VENDEDOR]
        ],
        [
            'path' => 'admin/inatividade',
            'roles' => [User::CODIGO_SUPERADMIN, User::CODIGO_ADMIN, User::CODIGO_GERENTE, User::CODIGO_VENDEDOR]
        ],

        //CLIENTE
        [
            'path' => 'cliente/meus_anuncios',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/anuncios',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/minha_conta',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/faq',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/home',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/pagamentos',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/listagem',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/contato',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/compra_plano',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/lojas',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/minhas_compras',
            'roles' => [User::CODIGO_CLIENTE]
        ],
        [
            'path' => 'cliente/notificacao_repasse',
            'roles' => [User::CODIGO_CLIENTE]
        ],
    ];
    
    public static function podeExecutarRota($currentPath, $userRole)
    {
        if ($userRole == User::CODIGO_SUPERADMIN) {
            return true;
        }

        foreach (AuthHelper::AUTH_MAP as $key => $item) {
            if(str_contains($currentPath, $item['path']) && in_array($userRole, $item['roles'])){
                return true;
            }
        }
        
        return false;
    }

    public static function verificaPropriedadeAnuncio($anuncio)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        if($anuncio->id_cliente == $cliente->id) return true;
        return false;
    }
}

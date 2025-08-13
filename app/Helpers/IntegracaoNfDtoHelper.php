<?php

namespace App\Helpers;

use DateTime;

class IntegracaoNfDtoHelper
{
    public static function getListaComissaoDTO($comissoes)
    {
        $comissoesDTO = [];
        foreach ($comissoes as $key => $comissao) {
            $comissaoDTO = [
                'id' => $comissao->id,
                'usuario' => IntegracaoNfDtoHelper::getUsuarioDTO($comissao->usuario),
                'percentual' => $comissao->percentual,
                'valor' => $comissao->valor,
                'data' => DateTime::createFromFormat('Y-m-d H:i:s', $comissao->created_at)->format('d/m/Y'),
            ];
            array_push($comissoesDTO, $comissaoDTO);
        }
        return $comissoesDTO;
    }

    public static function getUsuarioDTO($usuario)
    {
        $usuarioDTO = [
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'role' => $usuario->role,
        ];
        return $usuarioDTO;
    }

    public static function getListaClientesDTO($clientes)
    {
        $clientesDTO = [];
        foreach ($clientes as $key => $cliente) {
            $clienteDTO = IntegracaoNfDtoHelper::getClienteDTO($cliente);
            array_push($clientesDTO, $clienteDTO);
        }
        return $clientesDTO;
    }

    public static function getClienteDTO($cliente)
    {
        $clienteDTO = [
            'id' => $cliente->id,
            'nome' => $cliente->usuario->nome,
            'email' => $cliente->usuario->email,
            'tipo' => $cliente->tipo,
            'num_documento' => $cliente->num_documento,
            'data_nasc' => $cliente->data_nasc ? DateTime::createFromFormat('Y-m-d', $cliente->data_nasc)->format('d/m/Y') : null,
            'telefone' => $cliente->telefone,
            'celular' => $cliente->celular,
            'cep' => $cliente->cep,
            'logradouro' => $cliente->logradouro,
            'numero' => $cliente->numero,
            'bairro' => $cliente->bairro,
            'complemento' => $cliente->complemento,
            'cidade' => null,
            'nome_fantasia' => $cliente->nome_fantasia,
            'nome_responsavel' => $cliente->nome_responsavel,
            'cpf_responsavel' => $cliente->cpf_responsavel,
            'inscricao_estadual' => $cliente->inscricao_estadual,
            'rg' => $cliente->rg,
        ];

        if($cliente->cidade){
            $clienteDTO = [
                ...$clienteDTO,
                'cidade' => $cliente->cidade->nome . ' (' . $cliente->cidade->estado->sigla . ")",
            ];
        }
        
        return $clienteDTO;
    }

    public static function getListaPedidosDTO($itens, $exibePagamento = false)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = IntegracaoNfDtoHelper::getPedidoDTO($item, $exibePagamento);
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getPedidoDTO($item)
    {
        $itemDTO = [
            'id' => $item->id,
            'id_cliente' => $item->id_cliente,
            'nome_plano' => $item->nome_plano,
            'tipo_plano' => $item->tipo_plano,
            'tipo_plano_str' => $item->tipo_plano_str,
            'quant_anuncios' => $item->quant_anuncios,
            'marca_modelo' => $item->marca_modelo,
            'nome_proprietario' => $item->nome_proprietario,
            'localizacao_proprietario' => $item->localizacao_proprietario,
            'telefone_proprietario' => $item->telefone_proprietario,
            'celular_proprietario' => $item->celular_proprietario,
            'data_criacao' => $item->created_at->format('d/m/Y H:i'),
            'tipo_pedido' => $item->tipo_plano ? 'Compra de plano' : 
                ($item->anuncio && $item->anuncio->id_cliente == $item->id_cliente ? 'Criação de anúncio' : 'Compra de anúncio fechado'),
        ];
        if($item->cliente){
            $itemDTO['cliente'] = IntegracaoNfDtoHelper::getClienteDTO($item->cliente);
    
            if($item->cliente->cidade){
                $itemDTO['cliente']['cidade'] = $item->cliente->cidade->nome . ' (' . $item->cliente->cidade->estado->sigla . ")";
            }
        }
        if($item->pagamento){
            $itemDTO['pagamento'] = IntegracaoNfDtoHelper::getPagamentoDTO($item->pagamento);
        }
        return $itemDTO;
    }

    public static function getPagamentoDTO($item)
    {
        $itemDTO = [
            'id' => $item->id,
            'valor' => $item->valor,
            'codigo_forma_pagamento' => $item->formaPagamento->codigo,
            'forma_pagamento' => $item->formaPagamento->descricao,
            'codigo_status_pagamento' => $item->statusPagamento->codigo,
            'status_pagamento' => $item->statusPagamento->descricao,
        ];
        return $itemDTO;
    }
}

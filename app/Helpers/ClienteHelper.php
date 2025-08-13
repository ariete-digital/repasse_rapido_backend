<?php

namespace App\Helpers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;  

class ClienteHelper
{
    public static function isMinhaContaCompleta()
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        // Log::info(json_encode([
        //     'cliente' => $cliente,
        // ]));
        $isMinhaContaCompleta = $cliente->num_documento
            && $cliente->celular
            && $cliente->cep
            && $cliente->logradouro
            && $cliente->numero
            && $cliente->bairro
            && $cliente->id_cidade
            && $cliente->imagem_cnh
            && $cliente->imagem_comprovante;

        if($cliente->tipo == 'PJ'){
            $isMinhaContaCompleta = $isMinhaContaCompleta
                && $cliente->nome_fantasia
                && $cliente->nome_responsavel
                && $cliente->cpf_responsavel
                && $cliente->inscricao_estadual;
        } else {
            $isMinhaContaCompleta = $isMinhaContaCompleta
                && $cliente->data_nasc
                && $cliente->rg;
        }
        return $isMinhaContaCompleta;
    }

    public static function processarStatusInatividade()
    {
        
    }

    public static function generateUniqueSlug(string $name, string $table, string $column = 'slug', int $maxTries = 100): string
    {
        // slug base
        $slug = Str::slug($name);
        $originalSlug = $slug;

        $i = 1;

        // verifica existência no banco
        while (DB::table($table)->where($column, $slug)->exists()) {
            $slug = "{$originalSlug}-{$i}";
            $i++;

            if ($i > $maxTries) {
                throw new \RuntimeException("Não foi possível gerar um slug único para '{$name}'");
            }
        }

        return $slug;
    }
}

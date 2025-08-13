<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Cliente extends Model
{
    const BASE_UPLOAD_PATH = 'uploads' . DIRECTORY_SEPARATOR . 'minha_conta';

    use HasFactory;

    protected $fillable = [
        'id_usuario',
        'num_documento',
        'tipo',
        'data_nasc',
        'telefone',
        'celular',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'complemento',
        'id_cidade',
        'imagem_cnh',
        'imagem_comprovante',
        'imagem_doc_complementar',
        'nome_fantasia',
        'cpf_responsavel',
        'nome_responsavel',
        'id_dealer',
        'inscricao_estadual',
        'rg',
        'slug',
        'imagem_logo',
        'imagem_capa',
    ];

    protected $appends = [
        'url_cnh',
        'url_comprovante',
        'url_doc_complementar',
        'type_cnh',
        'type_comprovante',
        'type_doc_complementar',
        'cep_int',
    ];

    public function getUrlCnhAttribute()
    {
        return Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_cnh;
    }

    public function getUrlComprovanteAttribute()
    {
        return Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_comprovante;
    }

    public function getUrlDocComplementarAttribute()
    {
        return Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_doc_complementar;
    }

    public function getTypeCnhAttribute()
    {
        if(!$this->imagem_cnh) return null;
        $path = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_cnh;
        $mimeType = Storage::mimeType($path);
        return $mimeType;
    }

    public function getTypeComprovanteAttribute()
    {
        if(!$this->imagem_comprovante) return null;
        $path = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_comprovante;
        $mimeType = Storage::mimeType($path);
        return $mimeType;
    }

    public function getTypeDocComplementarAttribute()
    {
        if(!$this->imagem_doc_complementar) return null;
        $path = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . $this->imagem_doc_complementar;
        $mimeType = Storage::mimeType($path);
        return $mimeType;
    }

    public function getCepIntAttribute()
    {
        return intval(str_replace('.', '', str_replace('-', '', $this->cep)));
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'id_cidade');
    }

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class, 'id_cliente');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cliente');
    }

    public function pedidoMaisRecente()
    {
        return $this->hasOne(Pedido::class, 'id_cliente')->latestOfMany();
    }
}

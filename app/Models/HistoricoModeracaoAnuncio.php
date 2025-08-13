<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoModeracaoAnuncio extends Model
{
    const BASE_UPLOAD_PATH = 'uploads' . DIRECTORY_SEPARATOR . 'historico_moderacao';

    use HasFactory;

    protected $table = "historico_moderacao_anuncios";

    protected $fillable = [
        'id_anuncio',
        'codigo_anuncio',
        'email_usuario_moderacao',
        'nome_usuario_moderacao',
        'perfil_usuario_moderacao',
        'cnh_anunciante',
        'comprovante_anunciante',
        'doc_complementar_anunciante',
        'obs_moderacao',
    ];
}

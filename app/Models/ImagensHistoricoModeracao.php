<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagensHistoricoModeracao extends Model
{
    use HasFactory;

    protected $table = "imagens_historico_moderacao";

    protected $fillable = [
        'nome_arquivo',
        'id_historico',
    ];
}

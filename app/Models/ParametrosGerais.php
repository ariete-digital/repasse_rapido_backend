<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametrosGerais extends Model
{
    use HasFactory;

    CONST VALOR_COMISSAO_MODERADOR = 'VALOR_COMISSAO_MODERADOR';

    protected $fillable = [
        'chave',
        'valor',
    ];
}

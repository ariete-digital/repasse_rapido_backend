<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegracaoLojaConectada extends Model
{
    use HasFactory;

    protected $table = 'integracao_loja_conectada';

    protected $fillable = [
        'status',
        'resultado',
    ];
}

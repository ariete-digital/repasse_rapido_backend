<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AceiteTermoCliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_cliente',
        'id_anuncio',
        'aceite_termos_condicoes',
        'aceite_anuncio_fechado',
    ];
}

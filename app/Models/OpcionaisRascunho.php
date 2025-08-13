<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionaisRascunho extends Model
{
    use HasFactory;

    protected $table = "opcionais_rascunho";

    protected $fillable = [
        'id_opcional',
        'id_anuncio_rascunho',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionaisAnuncio extends Model
{
    use HasFactory;

    protected $table = "opcionais_anuncio";

    protected $fillable = [
        'id_opcional',
        'id_anuncio',
    ];
}

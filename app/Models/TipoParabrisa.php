<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoParabrisa extends Model
{
    use HasFactory;

    protected $table = "tipos_parabrisa";

    protected $fillable = [
        'descricao',
    ];
}

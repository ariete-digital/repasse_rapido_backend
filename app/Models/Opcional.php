<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcional extends Model
{
    use HasFactory;

    protected $table = "opcionais";

    protected $fillable = [
        'id_usuario',
        'descricao',
    ];
}

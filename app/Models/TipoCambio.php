<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoCambio extends Model
{
    use HasFactory;

    protected $table = "tipos_cambio";

    protected $fillable = [
        'descricao',
        'tipo_veiculo',
    ];
}

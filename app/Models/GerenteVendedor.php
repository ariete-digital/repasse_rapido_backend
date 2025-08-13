<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GerenteVendedor extends Model
{
    use HasFactory;

    protected $table = "gerentes_vendedores";

    protected $fillable = [
        'id_gerente',
        'id_vendedor',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalidadeSubregiao extends Model
{
    use HasFactory;

    protected $table = "localidades_subregioes";

    protected $fillable = [
        'id_subregiao',
        'id_uf',
        'cep_inicial',
        'cep_final',
    ];
}

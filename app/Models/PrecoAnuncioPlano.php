<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrecoAnuncioPlano extends Model
{
    use HasFactory;

    protected $table = "precos_anuncios_planos";

    protected $fillable = [
        'id_plano',
        'quant_anuncios',
        'preco',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }
}

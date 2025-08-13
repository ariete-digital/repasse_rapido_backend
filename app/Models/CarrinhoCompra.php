<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrinhoCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_cliente',
        'id_plano',
        'quant_anuncios',
        'id_anuncio',
        'info_extra',
    ];

    public function plano()
    {
        return $this->belongsTo(Plano::class, 'id_plano');
    }
}

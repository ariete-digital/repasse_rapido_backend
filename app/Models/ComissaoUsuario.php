<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComissaoUsuario extends Model
{
    use HasFactory;

    protected $table = "comissao_usuarios";

    protected $fillable = [
        'id_usuario',
        'id_pedido',
        'id_anuncio',
        'percentual',
        'valor'
    ];

    protected $appends = [
        'percentual_str',
        'valor_str',
    ];

    public function getPercentualStrAttribute()
    {
        return number_format($this->percentual, 2, ',', '.') . "%";
    }

    public function getValorStrAttribute()
    {
        return "R$ " . number_format($this->valor, 2, ',', '.');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function anuncio()
    {
        return $this->belongsTo(Anuncio::class, 'id_anuncio');
    }
}
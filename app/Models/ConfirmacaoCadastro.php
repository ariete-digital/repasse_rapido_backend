<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmacaoCadastro extends Model
{
    use HasFactory;

    protected $table = "confirmacao_cadastros";

    protected $fillable = [
        'id_cliente',
        'codigo',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}

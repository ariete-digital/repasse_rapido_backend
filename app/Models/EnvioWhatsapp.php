<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioWhatsapp extends Model
{
    use HasFactory;

    protected $table = "envios_whatsapp";

    protected $fillable = [
        'id_usuario',
        'numero',
        'mensagem',
        'zaapId',
        'messageId',
        'data_envio',
    ];
}

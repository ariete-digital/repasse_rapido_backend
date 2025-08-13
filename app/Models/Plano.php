<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plano extends Model
{
    use HasFactory;

    protected $table = "planos";

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
    ];

    protected $appends = [
        'tipo_str',
    ];

    public function getTipoStrAttribute()
    {
        if($this->tipo == 'D') return 'Destaque';
        if($this->tipo == 'A') return 'Aberto';
        if($this->tipo == 'F') return 'Fechado';
        return '';
    }

    public function precos()
    {
        return $this->hasMany(PrecoAnuncioPlano::class, 'id_plano');
    }
}

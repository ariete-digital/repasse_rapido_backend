<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cidade extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'id_uf',
        'id_ibge',
    ];

    public function estado()
    {
        return $this->belongsTo(Uf::class, 'id_uf');
    }
    
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_cidade');
    }

    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'banner_city_relationship', 'city_id', 'banner_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao',
        'tipo_veiculo',
    ];

    public function modelos()
    {
        return $this->hasMany(Modelo::class, 'id_marca');
    }
}

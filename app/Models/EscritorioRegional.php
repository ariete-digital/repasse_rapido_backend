<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscritorioRegional extends Model
{
    use HasFactory;

    protected $table = "escritorios_regionais";

    protected $fillable = [
        'id_usuario',
        'nome',
        'percentual_comissao',
        'endereco',
        'email',
        'telefone',
    ];

    protected $appends = [
        'percentual_comissao_str',
    ];

    public function getPercentualComissaoStrAttribute()
    {
        return number_format($this->percentual_comissao, 2, ',', '.') . "%";
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function ufs()
    {
        return $this->belongsToMany(Uf::class, 'ufs_escritorios', 'id_escritorio_regional', 'id_uf');
    }

    public function subregioes()
    {
        return $this->hasMany(Subregiao::class, 'id_escritorio_regional');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subregiao extends Model
{
    use HasFactory;

    protected $table = "subregioes";

    protected $fillable = [
        'id_escritorio_regional',
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
        return $this->belongsToMany(Uf::class, 'localidades_subregioes', 'id_subregiao', 'id_uf');
    }

    public function faixasCep()
    {
        return $this->hasMany(LocalidadeSubregiao::class, 'id_subregiao')->whereNotNull('cep_inicial')->whereNotNull('cep_final');
    }

    public function escritorio()
    {
        return $this->belongsTo(EscritorioRegional::class, 'id_escritorio_regional');
    }
}

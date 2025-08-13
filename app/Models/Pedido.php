<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_cliente',
        'id_anuncio',
        'nome_plano',
        'tipo_plano',
        'quant_anuncios',
        'marca_modelo',
        'versao_veiculo',
        'nome_proprietario',
        'localizacao_proprietario',
        'telefone_proprietario',
        'celular_proprietario',
        'info_extra',
    ];

    protected $appends = [
        'tipo_plano_str',
    ];

    public function getTipoPlanoStrAttribute()
    {
        if($this->tipo_plano == 'D') return 'Destaque';
        if($this->tipo_plano == 'A') return 'Aberto';
        if($this->tipo_plano == 'F') return 'Fechado';
        return '';
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function anuncio()
    {
        return $this->belongsTo(Anuncio::class, 'id_anuncio');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'id_pedido')->latest();
    }

    public function comissoes()
    {
        return $this->hasMany(ComissaoUsuario::class, 'id_pedido');
    }
}

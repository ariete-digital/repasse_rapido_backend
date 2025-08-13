<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnuncioRascunho extends Model
{
    use HasFactory;

    protected $table = "anuncios_rascunho";

    protected $fillable = [
        'id_anuncio_original',
        'id_cliente',
        'id_modelo',
        'id_cor',
        'id_tipo_cambio',
        'id_tipo_combustivel',
        'id_tipo_pneu',
        'id_tipo_parabrisa',
        'tipo_plano',
        'tipo_venda',
        'tipo_vendedor',
        'tipo_veiculo',
        'marca_veiculo',
        'modelo_veiculo',
        'submodelo',
        'valor_fipe',
        'renavam',
        'placa',
        'status_veiculo',
        'versao_veiculo',
        'ano_fabricacao',
        'ano_modelo',
        'quilometragem',
        'num_portas',
        'tipo_motor',
        'refrigeracao',
        'cilindrada',
        'partida',
        'freios',
        'tipo_freio',
        'alarme',
        'alimentacao',
        'controle_estabilidade',
        'roda_liga',
        'unico_dono',
        'tipo_troca',
        'ipva_pago',
        'veiculo_nome_anunciante',
        'financiado',
        'parcelas_em_dia',
        'aceita_financiamento',
        'todas_revisoes_concessionaria',
        'passou_leilao',
        'possui_manual',
        'possui_chave_reserva',
        'possui_ar',
        'ar_funcionando',
        'escapamento_solta_fumaca',
        'garantia_fabrica',
        'motor_bate',
        'cambio_faz_barulho',
        'cambio_escapa_marcha',
        'luz_injecao',
        'luz_airbag',
        'luz_abs',
        'tipo_monta',
        'furtado_roubado',
        'valor',
        'descricao',
        'aceite_termos'
    ];

    protected $appends = [
        'tipo_plano_str',
        'tipo_venda_str',
        'tipo_veiculo_str',
        'tipo_vendedor_str',
    ];

    public function getTipoPlanoStrAttribute()
    {
        if($this->tipo_plano == 'D') return 'Destaque';
        if($this->tipo_plano == 'A') return 'Aberto';
        if($this->tipo_plano == 'F') return 'Fechado';
        return '';
    }

    public function getTipoVendaStrAttribute()
    {
        if($this->tipo_venda == 'R') return 'Repasse';
        if($this->tipo_venda == 'C') return 'Consumidor final';
        return '';
    }

    public function getTipoVeiculoStrAttribute()
    {
        if($this->tipo_veiculo == 'C') return 'Carro';
        if($this->tipo_venda == 'M') return 'Moto';
        return '';
    }

    public function getTipoVendedorStrAttribute()
    {
        if($this->tipo_veiculo == 'R') return 'Revenda';
        if($this->tipo_venda == 'P') return 'Particular';
        return '';
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'id_modelo');
    }

    public function cor()
    {
        return $this->belongsTo(Cor::class, 'id_cor');
    }

    public function tipoCambio()
    {
        return $this->belongsTo(TipoCambio::class, 'id_tipo_cambio');
    }

    public function tipoCombustivel()
    {
        return $this->belongsTo(TipoCombustivel::class, 'id_tipo_combustivel');
    }

    public function tipoPneu()
    {
        return $this->belongsTo(TipoPneu::class, 'id_tipo_pneu');
    }

    public function tipoParabrisa()
    {
        return $this->belongsTo(TipoParabrisa::class, 'id_tipo_parabrisa');
    }

    public function opcionais()
    {
        return $this->belongsToMany(Opcional::class, 'opcionais_rascunho', 'id_anuncio_rascunho', 'id_opcional');
    }

    public function imagens()
    {
        return $this->hasMany(ImagensRascunho::class, 'id_anuncio_rascunho')->orderBy('principal', 'desc');
    }
}

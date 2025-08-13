<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anuncio extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'id_cliente',
        'id_modelo',
        'id_cor',
        'id_tipo_cambio',
        'id_tipo_combustivel',
        'id_tipo_pneu',
        'id_tipo_parabrisa',
        'ativo',
        'pausado',
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
        'aceite_termos',
        'moderacao_aprovada',
        'moderado_em',
        'obs_moderacao',
        'num_cliques',
        'id_usuario_moderacao',
        'id_ad_integrador',
    ];

    protected $appends = [
        'status_str',
        'moderacao_str',
        'tipo_plano_str',
        'tipo_monta_str',
        'tipo_troca_str',
        'unico_dono_str',
        'tipo_venda_str',
        'tipo_veiculo_str',
        'tipo_vendedor_str',
        'tipo_motor_str',
        'refrigeracao_str',
        'partida_str',
        'freios_str',
        'tipo_freio_str',
        'alimentacao_str',
        'is_vencido',
    ];

    public function getStatusStrAttribute()
    {
        if($this->ativo && !$this->pausado) return 'Ativo';
        if($this->ativo && $this->pausado) return 'Pausado';
        if(!$this->ativo) return 'Encerrado';
        return '';
    }

    public function getModeracaoStrAttribute()
    {
        if($this->moderacao_aprovada) return 'Moderação aprovada';
        if($this->moderacao_aprovada === 0) return 'Moderação reprovada';
        if($this->moderacao_aprovada == null) return 'Em moderação';
        return '';
    }

    public function getTipoPlanoStrAttribute()
    {
        if($this->tipo_plano == 'D') return 'Destaque';
        if($this->tipo_plano == 'A') return 'Aberto';
        if($this->tipo_plano == 'F') return 'Fechado';
        return '';
    }

    public function getTipoMontaStrAttribute()
    {
        if($this->tipo_monta == 'G') return 'Grande';
        if($this->tipo_monta == 'M') return 'Média';
        if($this->tipo_monta == 'P') return 'Pequena';
        if($this->tipo_monta == 'N') return 'Não ocorrido';
        return '';
    }

    public function getTipoTrocaStrAttribute()
    {
        if($this->tipo_troca == 'G') return 'Maior valor';
        if($this->tipo_troca == 'P') return 'Menor valor';
        if($this->tipo_troca == 'A') return 'Maior ou menor valor';
        if($this->tipo_troca == 'N') return 'Não aceito';
        return '';
    }

    public function getUnicoDonoStrAttribute()
    {
        if($this->unico_dono == 1) return 'Primeiro Dono';
        if($this->unico_dono == 2) return 'Segundo Dono';
        if($this->unico_dono == 3) return 'Indeterminado';
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
        if($this->tipo_veiculo == 'M') return 'Moto';
        return '';
    }

    public function getTipoVendedorStrAttribute()
    {
        if($this->tipo_vendedor == 'R') return 'Revenda';
        if($this->tipo_vendedor == 'P') return 'Particular';
        return '';
    }

    public function getTipoMotorStrAttribute()
    {
        if($this->tipo_motor == '2') return '2 tempos';
        if($this->tipo_motor == '4') return '4 tempos';
        if($this->tipo_motor == 'E') return 'Elétrico';
        return '';
    }

    public function getRefrigeracaoStrAttribute()
    {
        if($this->refrigeracao == 'R') return 'Ar';
        if($this->refrigeracao == 'G') return 'Água';
        return '';
    }

    public function getPartidaStrAttribute()
    {
        if($this->partida == 'P') return 'Pedal';
        if($this->partida == 'E') return 'Elétrica';
        return '';
    }

    public function getFreiosStrAttribute()
    {
        if($this->freios == 'M') return 'Disco diant. / Tambor tras.';
        if($this->freios == 'D') return 'Disco diant. / Disco tras.';
        if($this->freios == 'T') return 'Tambor diant. / Tambor tras.';
        return '';
    }

    public function getTipoFreioStrAttribute()
    {
        if($this->tipo_freio == 'A') return 'ABS';
        if($this->tipo_freio == 'C') return 'Convencional';
        return '';
    }

    public function getAlimentacaoStrAttribute()
    {
        if($this->alimentacao == 'C') return 'Carburador';
        if($this->alimentacao == 'I') return 'Injeção eletrônica';
        return '';
    }

    public function getIsVencidoAttribute()
    {
        if(!$this->data_validade) return false;
        return $this->data_validade < Carbon::now();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function usuarioModeracao()
    {
        return $this->belongsTo(User::class, 'id_usuario_moderacao');
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
        return $this->belongsToMany(Opcional::class, 'opcionais_anuncio', 'id_anuncio', 'id_opcional');
    }

    public function imagens()
    {
        return $this->hasMany(ImagensAnuncio::class, 'id_anuncio')->orderBy('principal', 'desc');
    }

    public static function geraCodigo(){
        $numeros = '012345678901234567890123456789';
        $seqNumeros = substr(str_shuffle($numeros), 0, 8);
        return $seqNumeros;
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaAnuncio extends Model
{
    use HasFactory;

    protected $table = 'licenca_anuncio';

    protected $fillable = [
        'id_cliente',
        'tipo_plano',
        'num_licencas',
        'data_vencimento',
    ];

    protected $appends = [
        'tipo_plano_str',
        'is_vencida',
    ];

    public function getTipoPlanoStrAttribute()
    {
        if($this->tipo_plano == 'D') return 'Destaque';
        if($this->tipo_plano == 'A') return 'Aberto';
        if($this->tipo_plano == 'F') return 'Fechado';
        return '';
    }

    public function getIsVencidaAttribute()
    {
        return $this->data_vencimento < Carbon::now()->setTime(0, 0, 0);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}

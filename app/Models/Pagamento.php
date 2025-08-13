<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_pedido',
        'id_status',
        'id_forma',
        'valor',
        'codigo',
        'external_reference',
        'status',
        'status_detail',
        'date_created',
        'date_last_updated',
        'date_of_expiration',
        'date_approved',
        'money_release_date',
        'payment_method_id',
        'payment_type_id',
        'operation_type',
        'binary_mode',
        'live_mode',
        'collector_id',
        'currency_id',
        'captured',
        'installments',
        'installment_amount',
        'token',
        'qr_code',
        'qr_code_base64',
        'ticket_url',
        'description',
        'issuer_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $appends = array(
        'is_expirado',
    );

    public function getIsExpiradoAttribute(){
        if(!$this->date_of_expiration) return false;
        return Carbon::parse($this->date_of_expiration) < Carbon::now();
    }

    public function statusPagamento()
    {
        return $this->belongsTo(StatusPagamento::class, 'id_status');
    }

    public function formaPagamento()
    {
        return $this->belongsTo(FormaPagamento::class, 'id_forma');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }
}

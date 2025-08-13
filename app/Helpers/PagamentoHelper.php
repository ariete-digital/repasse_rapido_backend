<?php

namespace App\Helpers;

use App\Models\Anuncio;
use App\Models\FormaPagamento;
use App\Models\LicencaAnuncio;
use App\Models\Pagamento;
use App\Models\StatusPagamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\SDK;
use MercadoPago\Payer;
use MercadoPago\Payment;

class PagamentoHelper {

    public static function fazerPagamento($requestData, $valor, $deviceID, $description)
    {
        $notificationUrl = route('cliente.pagamento.notificacao') . '?source_news=ipn';
        if (str_starts_with(Config::get('mercadopago.public_key'), 'TEST') || str_contains($notificationUrl,'http:')) {
            $notificationUrl = 'https://www.arietedigital.com.br';
        }

        $accessToken = Config::get('mercadopago.access_token');
        MercadoPagoConfig::setAccessToken($accessToken);

        $client = new PaymentClient();

        $header = [
            "X-meli-session-id" => $deviceID
        ];

        $options = new RequestOptions();
        $options->setCustomHeaders($header);

        try {
            $request = [
                ...$requestData,
                "transaction_amount" => (float)$valor,
                "description" => $description,
                "notification_url" => $notificationUrl,
            ];
            
            Log::info('request');
            Log::info($request);
            $payment = $client->create($request, $options);
            Log::info('payment');
            Log::info(json_encode(get_object_vars($payment), JSON_PRETTY_PRINT));

            return $payment;
    
        } catch (MPApiException $e) {
            Log::info("Status code: " . $e->getApiResponse()->getStatusCode() . "\n");
            Log::info("Content: " . json_encode($e->getApiResponse()->getContent()) . "\n");
            return false;
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public static function salvarPagamento($retornoPagamento, $paymentType, $valor, $idPedido)
    {
        $formaPagamento = FormaPagamento::where('codigo', $paymentType)->first();
        $statusPagamento = null;
        if ($retornoPagamento->status == 'approved' && $retornoPagamento->captured) {
            $statusPagamento = StatusPagamento::where('codigo', 'APROVADO')->first();
        } else if ($retornoPagamento->status == 'rejected') {
            $statusPagamento = StatusPagamento::where('codigo', 'RECUSADO')->first();
        } else {
            $statusPagamento = StatusPagamento::where('codigo', 'AGUARDANDO')->first();
        }

        $qrCode = null;
        if (isset($retornoPagamento->point_of_interaction->transaction_data->qr_code)) {
            $qrCode = $retornoPagamento->point_of_interaction->transaction_data->qr_code;
        }
        $qrCodeBase64 = null;
        if (isset($retornoPagamento->point_of_interaction->transaction_data->qr_code_base64)) {
            $qrCodeBase64 = $retornoPagamento->point_of_interaction->transaction_data->qr_code_base64;
        }
        $ticketUrl = null;
        if (isset($retornoPagamento->transaction_details->external_resource_url)) {
            $ticketUrl = $retornoPagamento->transaction_details->external_resource_url;
        }

        $pagamento = Pagamento::create([
            'id_pedido' => $idPedido,
            'id_forma' => $formaPagamento->id,
            'id_status' => $statusPagamento->id,
            'valor' => $valor,
            'id_status' => $statusPagamento->id,
            'id_forma' => $formaPagamento->id,
            'codigo' => $retornoPagamento->id,
            'external_reference' => $retornoPagamento->external_reference,
            'status' => $retornoPagamento->status,
            'status_detail' => $retornoPagamento->status_detail,
            'date_created' => $retornoPagamento->date_created,
            'date_last_updated' => $retornoPagamento->date_last_updated,
            'date_of_expiration' => $retornoPagamento->date_of_expiration,
            'date_approved' => $retornoPagamento->date_approved,
            'money_release_date' => $retornoPagamento->money_release_date,
            'payment_method_id' => $retornoPagamento->payment_method_id,
            'payment_type_id' => $retornoPagamento->payment_type_id,
            'operation_type' => $retornoPagamento->operation_type,
            'captured' => $retornoPagamento->captured,
            'binary_mode' => $retornoPagamento->binary_mode,
            'live_mode' => $retornoPagamento->live_mode,
            'collector_id' => $retornoPagamento->collector_id,
            'currency_id' => $retornoPagamento->currency_id,
            'installments' => $retornoPagamento->installments,
            'installment_amount' => $retornoPagamento->transaction_details->installment_amount,
            // 'token' => $retornoPagamento->token,
            'qr_code' => $qrCode,
            'qr_code_base64' => $qrCodeBase64,
            'ticket_url' => $ticketUrl,
            'description' => $retornoPagamento->description,
            'issuer_id' => $retornoPagamento->issuer_id,
        ]);

        return $pagamento;
    }

    public static function buscarPagamento($id){
        $url = 'https://api.mercadopago.com/v1/payments/' . $id;
        $headers = [
            'Content-Type: application/json',
			'Authorization: Bearer ' . Config::get('mercadopago.access_token')
        ];
    
        // Log::info($headers);
        $payment = PagamentoHelper::get($url, $headers);
        // Log::info($payment);
        return $payment;
    }

    private static function get($url, $headers){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		if (curl_errno($ch)) {
			$error_msg = curl_error($ch);
		}
		curl_close ($ch);
		if (isset($error_msg)) {
			Log::error($error_msg);
		}
		return json_decode($response, true);
	}

    public static function getMsgRetornoPagamento($status, $statusDetail){
        switch ($statusDetail) {
            case 'accredited':
                return 'Pagamento aprovado com sucesso!';
                break;
            case 'pending_contingency':
                return 'Estamos processando o pagamento.';
                break;
            case 'pending_review_manual':
                return 'Estamos processando o pagamento.';
                break;
            case 'cc_rejected_bad_filled_card_number':
                return 'Revise o número do cartão.';
                break;
            case 'cc_rejected_bad_filled_date':
                return 'Revise a data de vencimento.';
                break;
            case 'cc_rejected_bad_filled_other':
                return 'Revise os dados.';
                break;
            case 'cc_rejected_bad_filled_security_code':
                return 'Revise o código de segurança do cartão.';
                break;
            case 'cc_rejected_blacklist':
                return 'Não pudemos processar seu pagamento.';
                break;
            case 'cc_rejected_call_for_authorize':
                return 'Você deve autorizar o pagamento do valor ao Mercado Pago.';
                break;
            case 'cc_rejected_card_disabled':
                return 'Ligue para ativar seu cartão. O telefone está no verso do seu cartão.';
                break;
            case 'cc_rejected_card_error':
                return 'Não conseguimos processar seu pagamento.';
                break;
            case 'cc_rejected_duplicated_payment':
                return 'Você já efetuou um pagamento com esse valor. Caso precise pagar novamente, utilize outro cartão ou outra forma de pagamento.';
                break;
            case 'cc_rejected_high_risk':
                return 'Seu pagamento foi recusado. Escolha outra forma de pagamento. Recomendamos meios de pagamento em dinheiro.';
                break;
            case 'cc_rejected_insufficient_amount':
                return 'Saldo insuficiente.';
                break;
            case 'cc_rejected_invalid_installments':
                return 'Não é possível processar pagamentos com este numero de parcelas.';
                break;
            case 'cc_rejected_max_attempts':
                return 'Você atingiu o limite de tentativas permitido. Escolha outro cartão ou outra forma de pagamento.';
                break;
            case 'cc_rejected_other_reason':
                return 'Não foi possível processar o pagamento';
                break;
            case 'cc_rejected_card_type_not_allowed':
                return 'O pagamento foi rejeitado porque o usuário não tem a função crédito habilitada em seu cartão multiplo (débito e crédito).';
                break;

            default:
                if($status == 'approved'){
                    return 'Pagamento aprovado com sucesso!';
                } else if($status == 'pending'){
                    return 'Estamos aguardando o pagamento.';
                } else if($status == 'in_process'){
                    return 'Estamos processando o pagamento.';
                } else {
                    return 'Não foi possível processar o pagamento.';
                }
                break;
        }
    }

    public static function processaPagamentoAprovado($cliente, $pagamento, $contAnuncios, $pedido)
    {
        $statusAprovado = StatusPagamento::where('codigo', 'APROVADO')->first();
        if($pagamento->id_status == $statusAprovado->id && $cliente->tipo == 'PJ' && !$pedido->info_extra){
            $licencaAnuncio = LicencaAnuncio::where('id_cliente', $cliente->id)->where('tipo_plano', $pedido->tipo_plano)->first();
            if(!$licencaAnuncio){
                $licencaAnuncio = LicencaAnuncio::create([
                    'id_cliente' => $cliente->id,
                    'num_licencas' => $pedido->quant_anuncios,
                    'tipo_plano' => $pedido->tipo_plano,
                    'data_vencimento' => Carbon::now()->addDays(30),
                ]);
            }
            
            if($pedido->quant_anuncios != -1){
                $totalLicencas = $pedido->quant_anuncios - $contAnuncios;
            } else {
                $totalLicencas = -1;
            }

            $isLicencaVencida = $licencaAnuncio->is_vencida;
            
            $licencaAnuncio->num_licencas = $totalLicencas;
            $licencaAnuncio->tipo_plano = $pedido->tipo_plano;
            $licencaAnuncio->data_vencimento = Carbon::now()->addDays(30);
            $licencaAnuncio->save();

            if($isLicencaVencida){
                $anunciosRetomar = Anuncio::where('ativo', true)->where('pausado', true);
                if($pedido->quant_anuncios != -1){
                    $anunciosRetomar = $anunciosRetomar->limit($totalLicencas);
                }
                $anunciosRetomar = $anunciosRetomar->get();
                foreach ($anunciosRetomar as $key => $anuncio) {
                    $anuncio->pausado = false;
                    $anuncio->save();
                }
            }

            // ComissaoHelper::salvarComissaoVenda($pedido->id, $cliente->cidade->estado->id, $cliente->cep, $pagamento->valor);
        } else if($pagamento->id_status == $statusAprovado->id && $pedido->info_extra){
            $anuncio = Anuncio::where('id', $pedido->id_anuncio)->first();
            $anuncio->pausado = false;
            $anuncio->tipo_plano = $pedido->tipo_plano;
            if($pedido->info_extra == 'RENOVACAO'){
                $anuncio->data_validade = Carbon::now()->addDays(30);
            }
            $anuncio->save();
        }
    }
}

?>

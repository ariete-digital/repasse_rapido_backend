<?php 

namespace App\Helpers;

use App\Services\IntegratorService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IntegratorHelper {

	CONST URL_GET_DEALERS = "/v2/dealer";
	CONST URL_GET_INVENTORY = "/v2/inventory";

	public function run(){
		// busca todos os lojistas
		$urlDealers = Config::get('loja_conectada.url') . IntegratorHelper::URL_GET_DEALERS . '?page=1';
		$dealers = $this->getDealers($urlDealers);

		// atualiza os dados no banco
		$this->updateDatabaseDealers($dealers);

		// busca os anuncios de cada lojista na loja conectada
		$urlInventory = Config::get('loja_conectada.url') . IntegratorHelper::URL_GET_INVENTORY . '?page=1';
		$ads = $this->getInventory($urlInventory);
		// Log::info(json_encode([
		// 	'ads count' => count($ads)
		// ]));

		// cria/atualiza os anúncios
		$this->updateDatabaseAds($ads);
	}

	private function getDealers($url, &$allDealers = []){
		Log::info('------------------- nova chamada getDealers -------------------');
		Log::info('allDealers count = ' . count($allDealers));
		Log::info('url');
		Log::info($url);
		$headers = [
			'accept: application/json',
			'Authorization: Token ' . Config::get('loja_conectada.token')
		];
		$res = IntegratorHelper::get($url, $headers);
		if(isset($res['results'])){
			// Log::info('results count = ' . count($res['results']));
			$allDealers = array_merge($allDealers, $res['results']);
		}
		Log::info('allDealers + results count = ' . count($allDealers));
		if(isset($res['next']) && $res['next'] != null){
			$nextUrl = $res['next'];
			if(str_contains($nextUrl, 'http://')){
				$nextUrl = str_replace('http://', 'https://', $nextUrl);
			}
			$partialDealers = $this->getDealers($nextUrl, $allDealers);
			Log::info('partialDealers count = ' . count($partialDealers));
			return $partialDealers;
		}
		return $allDealers;
	}

	private function updateDatabaseDealers($dealers){
		DB::beginTransaction();
		try {
			$service = new IntegratorService();
			$service->processDealers($dealers);
			DB::commit();
		} catch (\Throwable $th) {
			Log::error($th);
			DB::rollBack();
			throw $th;
		}
	}

	private function getInventory($url, &$allAds = []){
		Log::info('------------------- nova chamada getInventory -------------------');
		Log::info('allAds count = ' . count($allAds));
		Log::info('url');
		Log::info($url);
		$headers = [
			'accept: application/json',
			'Authorization: Token ' . Config::get('loja_conectada.token')
		];
		$res = IntegratorHelper::get($url, $headers);
		if(isset($res['results'])){
			Log::info('results count = ' . count($res['results']));
			$allAds = array_merge($allAds, $res['results']);
		}
		Log::info('allAds + results count = ' . count($allAds));
		if(isset($res['next']) && $res['next'] != null){
			$nextUrl = $res['next'];
			if(str_contains($nextUrl, 'http://')){
				$nextUrl = str_replace('http://', 'https://', $nextUrl);
			}
			$partialAds = $this->getInventory($nextUrl, $allAds);
			Log::info('partialAds count = ' . count($partialAds));
			return $partialAds;
		}
		return $allAds;
	}

	public function updateDatabaseAds($ads){
		DB::beginTransaction();
		try {
			$service = new IntegratorService();
			$service->processAds($ads);
			DB::commit();
		} catch (\Throwable $th) {
			Log::error($th);
			DB::rollBack();
			throw $th;
		}
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
}

?>
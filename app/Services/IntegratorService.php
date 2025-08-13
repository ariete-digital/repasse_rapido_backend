<?php

namespace App\Services;

use App\Helpers\ClienteHelper;
use App\Models\Anuncio;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Cor;
use App\Models\ImagensAnuncio;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\OpcionaisAnuncio;
use App\Models\Opcional;
use App\Models\TipoCambio;
use App\Models\TipoCombustivel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IntegratorService {
    public function processDealers($dealers)
    {
        $idsNoBanco = [];
        foreach ($dealers as $key => $dealer) {
            // Log::info(json_encode([
            //     'dealer' => $dealer
            // ]));
            array_push($idsNoBanco, $dealer['dealer']);
            $cliente = Cliente::where('id_dealer', $dealer['dealer'])->with('usuario')->first();
            $nomeCidade = isset($dealer['addresses'][0]['city']['name']) ? $dealer['addresses'][0]['city']['name'] : null;
            $cidade = Cidade::where('nome', $nomeCidade)->first();
            // Log::info(json_encode([
            //     'cliente' => $cliente
            // ]));
            if($cliente){
                Log::info('Atualizando cadastro de cliente   ID: '.$cliente->id);
                $cliente->usuario->nome = $dealer['corporate_name'];
                $cliente->usuario->email = $dealer['email'];
                $cliente->usuario->password = Hash::make('quero102030');
                $cliente->usuario->save();
                $cliente->id_cidade = $cidade->id;
                $cliente->num_documento = $dealer['cnpj'];
                $cliente->tipo = 'PJ';
                $cliente->telefone = isset($dealer['phones'][0]) ? $dealer['phones'][0]['ddd'].$dealer['phones'][0]['phone'] : '';
                $cliente->cep = isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['zip_code'] : '';
                $cliente->logradouro = isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['address'] : '';
                $cliente->numero = isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['number'] : '';
                $cliente->bairro = isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['neighborhood'] : '';
                $cliente->complemento = isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['complement'] : '';
                $cliente->nome_fantasia = $dealer['name'];
                $cliente->slug = ClienteHelper::generateUniqueSlug($dealer['name'], 'clientes', 'slug');
                $cliente->save();
            } else {
                Log::info('Criando cadastro de cliente');
                $usuario = User::create([
                    'nome' => $dealer['corporate_name'],
                    'email' => $dealer['email'],
                    'password' => Hash::make('quero102030'),
                    'role' => 'cliente',
                    'active' => true,
                ]);
                $cliente = Cliente::create([
                    'id_cidade' => $cidade->id,
                    'id_usuario' => $usuario->id,
                    'id_dealer' => $dealer['dealer'],
                    'num_documento' => $dealer['cnpj'],
                    'tipo' => 'PJ',
                    'telefone' => isset($dealer['phones'][0]) ? $dealer['phones'][0]['ddd'].$dealer['phones'][0]['phone'] : '',
                    'cep' => isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['zip_code'] : '',
                    'logradouro' => isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['address'] : '',
                    'numero' => isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['number'] : '',
                    'bairro' => isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['neighborhood'] : '',
                    'complemento' => isset($dealer['addresses'][0]) ? $dealer['addresses'][0]['complement'] : '',
                    'nome_fantasia' => $dealer['name'],
                    'slug' => ClienteHelper::generateUniqueSlug($dealer['name'], 'clientes', 'slug'),
                ]);
                Log::info('Cliente ID: '.$cliente->id);
            }
        }
        $clientesDelete = Cliente::whereNotNull('id_dealer')->whereNotIn('id_dealer', $idsNoBanco)->with('usuario', 'anuncios')->get();
        foreach ($clientesDelete as $key => $cli) {
            Log::info('Deletando cliente...   ID: '.$cli->id);
            foreach ($cli->anuncios as $key => $a) {
                $a->delete();
            }
            Log::info('Anuncios deletados');
            $idUsuario = $cli->usuario->id;
            $cli->delete();
            User::where('id', $idUsuario)->delete();
            Log::info('Cliente deletado');
        }
    }

    public function processAds($ads)
    {
        $idsNoBanco = [];
        foreach ($ads as $key => $ad) {
            // Log::info('ad =');
            // Log::info($ad);
            array_push($idsNoBanco, $ad['ad_id']);
            $anuncio = Anuncio::where('id_ad_integrador', $ad['ad_id'])->first();
            
            $tipoCombustivel = TipoCombustivel::where('descricao', $ad['fuel']['name'])->first();
            if(!$tipoCombustivel) $tipoCombustivel = TipoCombustivel::create(['descricao' => $ad['fuel']['name']]);
            
            $cor = Cor::where('descricao', $ad['color']['name'])->first();
            if(!$cor) $cor = Cor::create(['descricao' => $ad['color']['name']]);

            $tipoVeiculo = $ad['category']['id'] == 1 ? 'C' : 'M';
            $statusVeiculo = $ad['is_new'] == true ? 'N' : 'U';
            
            $marca = Marca::where('descricao', $ad['manufacturer']['name'])->first();
            if(!$marca) $marca = Marca::create(['descricao' => $ad['manufacturer']['name'], 'tipo_veiculo' => $tipoVeiculo]);
            
            $nomeModelo = $ad['model']['name'];
            if(isset($ad['version']) && isset($ad['version']['name'])){
                $nomeModelo = $nomeModelo . ' ' . $ad['version']['name'];
            }
            $modelo = Modelo::where('descricao', $nomeModelo)->first();
            if(!$modelo) $modelo = Modelo::create(['descricao' => $nomeModelo, 'id_marca' => $marca->id, 'nome_curto' => $ad['model']['name']]);
            
            $tipoCambio = null;
            if(isset($ad['transmission']) && isset($ad['transmission']['name'])){
                $tipoCambio = TipoCambio::where('descricao', $ad['transmission']['name'])->first();
                if(!$tipoCambio) $tipoCambio = TipoCambio::create(['descricao' => $ad['transmission']['name'], 'tipo_veiculo' => $tipoVeiculo]);
            } else {
                $tipoCambio = TipoCambio::firstOrCreate(['descricao' => 'NAO INFORMADO'], ['tipo_veiculo' => $tipoVeiculo]);
            }

            if($anuncio){
                Log::info('Atualizando anuncio ad_id ='.$ad['ad_id']);
                $anuncio->id_modelo = $modelo->id;
                $anuncio->id_cor = $cor->id;
                $anuncio->id_tipo_cambio = $tipoCambio->id;
                $anuncio->id_tipo_combustivel = $tipoCombustivel->id;
                $anuncio->ativo = true;
                $anuncio->pausado = false;
                $anuncio->tipo_venda = 'C';
                $anuncio->tipo_vendedor = 'R';
                $anuncio->tipo_veiculo = $tipoVeiculo;
                $anuncio->placa = $ad['license_plate'];
                $anuncio->status_veiculo = $statusVeiculo;
                $anuncio->ano_fabricacao = $ad['make_year'];
                $anuncio->ano_modelo = $ad['model_year'];
                $anuncio->quilometragem = $ad['km'];
                $anuncio->num_portas = isset($ad['doors']) ? $ad['doors'] : null;
                $anuncio->unico_dono = $ad['only_owner'] == true;
                $anuncio->ipva_pago = $ad['tax_payed'] == true;
                $anuncio->garantia_fabrica = $ad['factory_warranty'] == true;
                $anuncio->valor = $ad['price'];
                $anuncio->descricao = $ad['description'];
                $anuncio->aceite_termos = true;
                $anuncio->moderacao_aprovada = true;
                $anuncio->moderado_em = Carbon::now();
                $anuncio->obs_moderacao = 'Importado do Loja Conectada';
                $anuncio->marca_veiculo = $marca->descricao;
                $anuncio->modelo_veiculo = $nomeModelo;
                $anuncio->submodelo = $ad['model']['name'];
                $anuncio->save();
            } else {
                Log::info('Criando anuncio ad_id ='.$ad['ad_id']);
                $cliente = Cliente::where('id_dealer', $ad['dealer'])->with('usuario')->first();
                $anuncio = Anuncio::create([
                    'id_cliente' => $cliente->id,
                    'id_modelo' => $modelo->id,
                    'id_cor' => $cor->id,
                    'id_tipo_cambio' => $tipoCambio->id,
                    'id_tipo_combustivel' => $tipoCombustivel->id,
                    'ativo' => true,
                    'pausado' => false,
                    'tipo_plano' => 'A',
                    'tipo_venda' => 'C',
                    'tipo_vendedor' => 'R',
                    'tipo_veiculo' => $tipoVeiculo,
                    'placa' => $ad['license_plate'],
                    'status_veiculo' => $statusVeiculo,
                    'ano_fabricacao' => $ad['make_year'],
                    'ano_modelo' => $ad['model_year'],
                    'quilometragem' => $ad['km'],
                    'num_portas' => isset($ad['doors']) ? $ad['doors'] : null,
                    'unico_dono' => $ad['only_owner'] == true,
                    'ipva_pago' => $ad['tax_payed'] == true,
                    'garantia_fabrica' => $ad['factory_warranty'] == true,
                    'valor' => $ad['price'],
                    'descricao' => $ad['description'],
                    'aceite_termos' => true,
                    'moderacao_aprovada' => true,
                    'moderado_em' => Carbon::now(),
                    'obs_moderacao' => 'Importado do Loja Conectada',
                    'marca_veiculo' => $marca->descricao,
                    'modelo_veiculo' => $nomeModelo,
                    'submodelo' => $ad['model']['name'],
                    'codigo' => Anuncio::geraCodigo(),
                    'id_ad_integrador' => $ad['ad_id'],
                ]);
                // Log::info($anuncio);
            }

            $idsOpcionaisInseridos = [];
            foreach ($ad['optionals'] as $key => $opt) {
                $optional = Opcional::where('descricao', $opt['name'])->first();
                if(!$optional) $optional = Opcional::create(['descricao' => $opt['name']]);
                OpcionaisAnuncio::updateOrCreate(
                    [
                        'id_anuncio' => $anuncio->id,
                        'id_opcional' => $optional->id,
                    ],[]
                );
                array_push($idsOpcionaisInseridos, $optional->id);
            }
            OpcionaisAnuncio::where('id_anuncio', $anuncio->id)->whereNotIn('id_opcional', $idsOpcionaisInseridos)->delete();

            $linksImagemInseridos = [];
            foreach ($ad['photos'] as $key => $photo) {
                //quando ja tiver o anuncio criado, verifica se a foto ja existe, se não existir, cria uma.
                $imagem = ImagensAnuncio::where('id_anuncio', $anuncio->id)->where('link', $photo['photo'])->first();
                if(!$imagem){
                    $imagem = ImagensAnuncio::create([
                        'id_anuncio' => $anuncio->id,
                        'arquivo' => $photo['photo'],
                        'link' => $photo['photo'],
                        'principal' => $photo['order'] == 1,
                    ]);
                }
                array_push($linksImagemInseridos, $imagem->link);
            }
            ImagensAnuncio::where('id_anuncio', $anuncio->id)->whereNotIn('link', $linksImagemInseridos)->delete();
        }
        $anunciosDelete = Anuncio::whereNotNull('id_ad_integrador')->whereNotIn('id_ad_integrador', $idsNoBanco)->get();
        foreach ($anunciosDelete as $key => $anuncio) {
            $anuncio->ativo = false;
            $anuncio->save();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\AnuncioHelper;
use App\Models\Anuncio;
use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnunciosService
{
    public function getAnuncios(
        ?string $tipoVeiculo = null,
        ?string $tipoVenda = null,
        ?int $idCidade = null,
        ?int $idEstado = null,
        ?int $idMarca = null,
        ?int $idModelo = null,
        ?string $versaoVeiculo = null,
        ?string $statusVeiculo = null,
        ?string $anoMin = null,
        ?string $anoMax = null,
        ?string $valorMin = null,
        ?string $valorMax = null,
        ?string $quilometragemMin = null,
        ?string $quilometragemMax = null,
        ?array $tiposVendedor = null,
        ?array $tiposCambio = null,
        ?array $tiposCombustivel = null,
        ?array $numPortas = null,
        ?string $idCor = null,
        ?array $opcionais = null,
        ?string $tipoPlano = null,
        ?int $idCliente = null,
        ?string $slug = null,
        ?int $limit = null,
        ?int $offset = null,
        ?bool $isRandomOrder = false,
        ?string $ordenacao = null,
        ?string $nomeMarca = null,
        ?string $nomeModelo = null,
    ) {
        // DB::listen(function ($query) {
        //     Log::info(
        //         vsprintf(str_replace('?', '%s', $query->sql), $query->bindings)
        //     );
        // });
        
        $anuncios = AnuncioHelper::getAnuncioBaseQuery();

        if ($tipoVeiculo) {
            $anuncios->where('tipo_veiculo', $tipoVeiculo);
        }
        if ($tipoVenda) {
            $anuncios->where('tipo_venda', $tipoVenda);
        }
        if ($idCidade) {
            $anuncios->whereRelation('cliente', 'id_cidade', '=', $idCidade);
        }
        if ($idEstado) {
            $anuncios->whereRelation('cliente.cidade', 'id_uf', '=', $idEstado);
        }
        if ($idMarca && !$idModelo) {
            $marca = Marca::where('id', $idMarca)->first();
            // Log::info('marca');
            // Log::info($marca);
            // $anuncios->whereRelation('modelo', 'id_marca', '=', $idMarca);
            $anuncios->where(function ($query) use ($idMarca, $marca) {
                $query->whereRelation('modelo', 'id_marca', '=', $idMarca)
                    ->orWhere(function ($subquery) use ($marca) {
                        $subquery->whereNull('id_modelo')
                            ->whereRaw('LOWER(marca_veiculo) LIKE ?', ['%' . strtolower($marca->descricao) . '%']);
                });
            });
        }
        if ($idModelo) {
            $modelo = Modelo::where('id', $idModelo)->first();
            // Log::info('modelo');
            // Log::info($modelo);
            $anuncios->where('submodelo', 'like', '%'.$modelo->nome_curto.'%');
        }
        if ($nomeMarca) {
            $anuncios->where('marca_veiculo', 'like', '%'.trim($nomeMarca).'%');
        }
        if ($nomeModelo) {
            $anuncios->where('modelo_veiculo', 'like', '%'.trim($nomeModelo).' %');
        }
        if ($idCliente) {
            $anuncios->where('id_cliente', $idCliente);
        }
        if ($versaoVeiculo) {
            $anuncios->where('versao_veiculo', $versaoVeiculo);
        }
        if ($statusVeiculo) {
            $anuncios->where('status_veiculo', $statusVeiculo);
        }
        if ($anoMin) {
            $anuncios->where(function ($query) use ($anoMin) {
                $query
                    ->where('ano_fabricacao', '>=', $anoMin)
                    ->where('ano_modelo', '>=', $anoMin)
                ;
            });
        }
        if ($anoMax) {
            $anuncios->where(function ($query) use ($anoMax) {
                $query
                    ->where('ano_fabricacao', '<=', $anoMax)
                    ->where('ano_modelo', '<=', $anoMax)
                ;
            });
        }
        if ($valorMin) {
            $anuncios->where('valor', '>=', $valorMin);
        }
        if ($valorMax) {
            $anuncios->where('valor', '<=', $valorMax);
        }
        if ($quilometragemMin) {
            $anuncios->where('quilometragem', '>=', $quilometragemMin);
        }
        if ($quilometragemMax) {
            $anuncios->where('quilometragem', '<=', $quilometragemMax);
        }
        if ($tiposVendedor) {
            $anuncios->whereIn('tipo_vendedor', $tiposVendedor);
        }
        if ($tiposCambio) {
            $anuncios->whereIn('id_tipo_cambio', $tiposCambio);
        }
        if ($tiposCombustivel) {
            $anuncios->whereIn('id_tipo_combustivel', $tiposCombustivel);
        }
        if ($numPortas) {
            $anuncios->whereIn('num_portas', $numPortas);
        }
        if ($idCor) {
            $anuncios->where('id_cor', $idCor);
        }
        if ($opcionais) {
            $anuncios->whereHas('opcionais', function ($query) use ($opcionais) {
                $query->whereIn('opcionais.id', $opcionais);
            });
        }
        if ($tipoPlano && $tipoPlano === 'D') {
            $anuncios->where('tipo_plano', 'D');
        }
        if ($tipoPlano && $tipoPlano !== 'D') {
            $anuncios->where('tipo_plano', '<>', 'D');
        }
        if ($slug) {
            $anuncios->whereRelation('cliente', 'clientes.slug', '=', $slug);
        }

        if($isRandomOrder){
            $anuncios->inRandomOrder();
        } else {
            if($ordenacao == 'N'){
                $anuncios->orderBy('valor');
            } else if($ordenacao == 'P'){
                $anuncios->orderBy('valor', 'desc');
            } else if($ordenacao == 'A'){
                $anuncios->orderBy('ano_modelo', 'desc');
            } else if($ordenacao == 'K'){
                $anuncios->orderBy('quilometragem');
            } else {
                $anuncios->orderBy('created_at');
            }
        }

        $anuncios->orderBy('marca_veiculo')->orderBy('modelo_veiculo');
        // Log::info($anuncios->toSql());
        $anuncios->with('cliente', 'modelo.marca', 'cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa', 'imagens');

        $anunciosQuery = clone $anuncios;
        $total = $anunciosQuery->count();

        if (!is_null($limit) && !is_null($offset)) {
            $anuncios->offset($offset)->limit($limit);
        } else if(is_null($limit) && is_null($offset)){
            $anuncios->limit(100);
        }

        // Log::info('sql = '.$anuncios->toSql());
        
        return [
            'items' => $anuncios->get(),
            'total' => $total,
        ];
    }
}

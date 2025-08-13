<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Marca;
use App\Models\Modelo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ModelosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->populaMarcasCarro();
        $this->populaMarcasMoto();
    }

    public function populaMarcasCarro(): void
    {
        $urlMarcasCarro = 'https://parallelum.com.br/fipe/api/v1/carros/marcas';
        $urlModelosCarroBase = 'https://parallelum.com.br/fipe/api/v1/carros/marcas/???/modelos';

        $marcas = $this->get($urlMarcasCarro);
        // Log::info(json_encode($marcas));

        foreach ($marcas as $key => $marca) {
            $ma = Marca::updateOrCreate(
                [
                    'id' => $marca['codigo'],
                    'descricao' => $marca['nome'],
                    'tipo_veiculo' => 'C',
                ]
            );

            $urlModelos = str_replace('???', $ma->id, $urlModelosCarroBase);
            $modelos = $this->get($urlModelos);
            foreach ($modelos['modelos'] as $key => $modelo) {
                // Log::info(json_encode($modelo));
                $arrayNomesModelo = explode(" ", $modelo['nome']);
                $nomeCurto = $arrayNomesModelo[0];
                if(str_starts_with(strtoupper($modelo['nome']), "GRAND") || str_starts_with(strtoupper($modelo['nome']), "DEL")){
                    $nomeCurto = $nomeCurto . " " . $arrayNomesModelo[1];
                }
                $mo = Modelo::updateOrCreate(
                    [
                        'id'=> $modelo['codigo'],
                    ],
                    [
                        'id_marca' => $ma->id,
                        'descricao' => $modelo['nome'],
                        'nome_curto' => $nomeCurto,
                    ]
                );
            }
        }
    }

    public function populaMarcasMoto(): void
    {
        $urlMarcasMoto = 'https://parallelum.com.br/fipe/api/v1/motos/marcas';
        $urlModelosMotoBase = 'https://parallelum.com.br/fipe/api/v1/motos/marcas/???/modelos';

        $marcas = $this->get($urlMarcasMoto);
        // Log::info(json_encode($marcas));

        foreach ($marcas as $key => $marca) {
            $ma = Marca::updateOrCreate(
                [
                    'id' => $marca['codigo'],
                ],
                [
                    'descricao' => $marca['nome'],
                    'tipo_veiculo' => 'M',
                ]
            );

            $urlModelos = str_replace('???', $ma->id, $urlModelosMotoBase);
            $modelos = $this->get($urlModelos);
            foreach ($modelos['modelos'] as $key => $modelo) {
                // Log::info(json_encode($modelo));
                $arrayNomesModelo = explode(" ", $modelo['nome']);
                $nomeCurto = $arrayNomesModelo[0];
                if(str_starts_with(strtoupper($modelo['nome']), "GRAND") || str_starts_with(strtoupper($modelo['nome']), "DEL")){
                    $nomeCurto = $nomeCurto . " " . $arrayNomesModelo[1];
                }
                $mo = Modelo::updateOrCreate(
                    [
                        'id'=> $modelo['codigo'],
                    ],
                    [
                        'id_marca' => $ma->id,
                        'descricao' => $modelo['nome'],
                        'nome_curto' => $nomeCurto,
                    ]
                );
            }
        }
    }

    public function get($url)
    {
        $cURLConnection = curl_init($url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        return json_decode($apiResponse, true);
    }
}

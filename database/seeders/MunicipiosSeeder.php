<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cidade;
use App\Models\Uf;
use Illuminate\Database\Seeder;

class MunicipiosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {        
        $data = file_get_contents('docs/carga/municipios.json');
        $array = json_decode($data, true);
        
        // $cidades = [];
        foreach ($array as $key => $item) {
            $uf = Uf::updateOrCreate(
                ['sigla' => $item['UF-sigla']],
                [
                    'nome' => $item['UF-nome']." (".$item['UF-sigla'].")",
                    'id_ibge' => $item['UF-id']
                ]
            );
            
            Cidade::updateOrCreate(
                [
                    'id_ibge' => $item['municipio-id']
                ],
                [
                    'nome' => $item['municipio-nome'],
                    'id_uf' => $uf->id,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cor;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoCambio;
use App\Models\TipoCombustivel;
use App\Models\TipoParabrisa;
use App\Models\TipoPneu;
use Faker\Factory;
use Illuminate\Database\Seeder;

class AAACadastrosBasicosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->populaModelos();
        $this->populaTipoCambio();
        $this->populaTipoCombustivel();
        $this->populaTipoPneu();
        $this->populaTipoParabrisa();
        $this->populaCor();
    }

    private function populaModelos(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $marca = Marca::updateOrCreate(
                ['descricao' => $faker->company],
                ['tipo_veiculo' => $faker->randomElement(['C', 'M'])]
            );
            for ($j = 0; $j < 10; $j++) {
                Modelo::firstOrCreate([
                    'descricao' => $faker->word,
                    'id_marca' => $marca->id,
                    'nome_curto' => $faker->name
                ]);
            }
        }
    }

    private function populaTipoCambio(): void
    {
        $faker = Factory::create();
        $tipos = [
            [
                'descricao' => 'Manual',
                'tipo_veiculo' => $faker->randomElement(['C', 'M'])
            ],
            [
                'descricao' => 'Automático',
                'tipo_veiculo' => $faker->randomElement(['C', 'M'])
            ],
            [
                'descricao' => 'Automatizado',
                'tipo_veiculo' => $faker->randomElement(['C', 'M'])
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoCambio::firstOrCreate($modelo);
        }
    }

    private function populaTipoCombustivel(): void
    {
        $tipos = [
            [
                'descricao' => 'Álcool',
            ],
            [
                'descricao' => 'Gasolina',
            ],
            [
                'descricao' => 'Flex',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoCombustivel::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaTipoPneu(): void
    {
        $tipos = [
            [
                'descricao' => 'On-road',
            ],
            [
                'descricao' => 'Off-road',
            ],
            [
                'descricao' => 'Misto',
            ],
            [
                'descricao' => 'Radial',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoPneu::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaTipoParabrisa(): void
    {
        $tipos = [
            [
                'descricao' => 'Convencional',
            ],
            [
                'descricao' => 'Temperado',
            ],
            [
                'descricao' => 'Laminado',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoParabrisa::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaCor(): void
    {
        $tipos = [
            [
                'descricao' => 'Branco',
            ],
            [
                'descricao' => 'Prata',
            ],
            [
                'descricao' => 'Violeta',
            ],
            [
                'descricao' => 'Vermelho',
            ],
            [
                'descricao' => 'Cinza',
            ],
            [
                'descricao' => 'Preto',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = Cor::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Anuncio;
use App\Models\Cliente;
use App\Models\Cor;
use App\Models\ImagensAnuncio;
use App\Models\Modelo;
use App\Models\TipoCambio;
use App\Models\TipoCombustivel;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class AnunciosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $user = User::create(
            [
                'email' => $faker->email,
                'nome' => $faker->name(),
                'password' => 123,
                'role' => 'cliente',
                'active' => 1,
            ]
        );

        $cars_images = [
            "https://th.bing.com/th/id/OIP.JwtzlWEzB1o1QCxEjUmD_AHaE8?w=272&h=182&c=7&r=0&o=5&pid=1.7",
            "https://th.bing.com/th/id/OIP.fm2t6tTfPrQZoBvYpwMXcQHaE5?w=275&h=182&c=7&r=0&o=5&pid=1.7",
            "https://th.bing.com/th/id/OIP.c7-GlP-bjWCrJiJXwQJAOAHaFj?w=207&h=180&c=7&r=0&o=5&pid=1.7",
            "https://img.freepik.com/vetores-gratis/carro-esportivo-azul-isolado-no-branco-vector_53876-67354.jpg"
        ];

        $cliente = Cliente::firstOrCreate(
            ['id_usuario' => $user->id],
            [
                'tipo' => 'PF',
            ]
        );

        $modelos = Modelo::get();
        $cor = Cor::first();
        $tipoCambio = TipoCambio::first();
        $tipoCombustivel = TipoCombustivel::first();

        foreach($modelos as $modelo) {
            $tipoPlano = $faker->randomElement(['A', 'D', 'F']);
            $anuncio = Anuncio::create([
                'codigo' => $faker->randomNumber(5),
                'id_cliente' => $cliente->id,
                'id_modelo' => $modelo->id,
                'id_cor' => $cor->id,
                'id_tipo_cambio' => $tipoCambio->id,
                'id_tipo_combustivel' => $tipoCombustivel->id,
                'tipo_plano' => $tipoPlano,
                'tipo_venda' => 'C',
                'tipo_vendedor' => 'P',
                'tipo_veiculo' => $faker->randomElement(['C', 'M']),
                'marca_veiculo' => $modelo->marca->descricao,
                'modelo_veiculo' => $modelo->descricao,
                'submodelo' => $modelo->nome_curto,
                'renavam' => $faker->randomNumber(9),
                'placa' => $faker->randomNumber(7),
                'status_veiculo' => 'U',
                'versao_veiculo' => '2.0 turbo',
                'ano_fabricacao' =>  $faker->year(),
                'ano_modelo' => $faker->year(),
                'quilometragem' => $faker->randomNumber(5),
                'num_portas' => 4,
                'unico_dono' => 1,
                'valor' => $faker->randomNumber(5),
                'descricao' => $faker->text(200),
                'aceite_termos' => 1,
                'moderacao_aprovada' => 1,
            ]);
        }
    }
}

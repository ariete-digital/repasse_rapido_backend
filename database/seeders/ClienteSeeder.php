<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            Cliente::firstOrCreate(
                [
                    'id_usuario' => 1,
                    'num_documento' => $faker->randomNumber(9),
                    'tipo' => 'PJ',
                    'data_nasc' => $faker->date(),
                    'telefone' => $faker->phoneNumber,
                    'celular' => $faker->phoneNumber,
                    'cep' => $faker->postcode,
                    'logradouro' => $faker->streetName,
                    'numero' => $faker->buildingNumber,
                    'bairro' => $faker->city,
                    'complemento' => $faker->secondaryAddress,
                    'id_cidade' => 1,
                    'imagem_cnh' => "",
                    'imagem_comprovante' => "",
                    'nome_fantasia' => $faker->company,
                    'cpf_responsavel' => $faker->randomNumber(9),
                    'nome_responsavel' => $faker->name,
                ]
            );
        }
    }
}

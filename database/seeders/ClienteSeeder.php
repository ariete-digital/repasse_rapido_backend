<?php

namespace Database\Seeders;

use App\Helpers\ClienteHelper;
use App\Models\Cliente;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClienteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create('pt_BR');
        for ($i = 0; $i < 10; $i++) {       
            $user = User::firstOrCreate(
                [
                    'nome' => $faker->name,
                    'email' => $faker->email,
                    'password' => Hash::make('123'),
                    'role' => 'cliente',
                    'active' => true
                ]
            );
            $tipoCliente = 'PF';
            if($i >= 3) $tipoCliente = 'A';
            if($i >= 6) $tipoCliente = 'PJ';
            
            $cliente = Cliente::firstOrCreate(
                [
                    'id_usuario' => $user->id,
                    'num_documento' => $faker->randomNumber(9),
                    'tipo' => $tipoCliente,
                    'data_nasc' => $faker->date(),
                    'telefone' => $faker->phoneNumber,
                    'celular' => $faker->phoneNumber,
                    'cep' => $faker->postcode,
                    'logradouro' => $faker->streetName,
                    'numero' => $faker->buildingNumber,
                    'bairro' => $faker->city,
                    'complemento' => $faker->secondaryAddress,
                    'id_cidade' => 2310,
                    'imagem_cnh' => "",
                    'imagem_comprovante' => "",
                    'imagem_doc_complementar' => "",
                    'nome_fantasia' => $faker->company,
                    'cpf_responsavel' => $faker->randomNumber(9),
                    'nome_responsavel' => $faker->name,
                    'inscricao_estadual' => $faker->randomNumber(6),
                    'rg' => $faker->randomNumber(8),
                    'slug' => ClienteHelper::generateUniqueSlug($user->nome, 'clientes', 'slug'),
                    'imagem_logo' => "",
                    'imagem_capa' => "",
                ]
            );
        }
    }
}

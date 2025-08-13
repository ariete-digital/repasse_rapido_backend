<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LojasSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nome' => 'AG Automóveis',
                'email' => 'agautomoveis@teste.com',
                'password' => Hash::make('123'),
                'role' => 'cliente',
                'active' => true,
                //cliente
                'tipo' => 'PJ',
                'cep' => null,
                'telefone' => '(31) 4141-4344',
                'celular' => '(31) 4104-4344',
                'logradouro' => 'Av. Dom Pedro II',
                'numero' => '1.900',
                'complemento' => '1º Piso Loja 105',
                'bairro' => 'Santo André',
                'id_cidade' => '2310',
                'nome_fantasia' => 'AG Automóveis',
            ],
            [
                'nome' => 'BH Autos',
                'email' => 'bhautos@teste.com',
                'password' => Hash::make('123'),
                'role' => 'cliente',
                'active' => true,
                //cliente
                'tipo' => 'PJ',
                'cep' => null,
                'telefone' => '(31) 3292-6666',
                'celular' => null,
                'logradouro' => 'Av. do Contorno',
                'numero' => '8686',
                'complemento' => null,
                'bairro' => 'Santo Agostinho',
                'id_cidade' => '2310',
                'nome_fantasia' => 'BH Autos',
            ],
            [
                'nome' => 'Localiza Seminovos',
                'email' => 'localiza@teste.com',
                'password' => Hash::make('123'),
                'role' => 'cliente',
                'active' => true,
                //cliente
                'tipo' => 'PJ',
                'cep' => '30710-010',
                'telefone' => '(31) 2108-8550',
                'celular' => null,
                'logradouro' => 'Av. Dom Pedro II',
                'numero' => '1.900',
                'complemento' => 'Loja 120 a 124',
                'bairro' => 'Carlos Prates',
                'id_cidade' => '2310',
                'nome_fantasia' => 'Localiza Seminovos',
            ],
            [
                'nome' => 'Top Carro BH',
                'email' => 'topcarrobh@teste.com',
                'password' => Hash::make('123'),
                'role' => 'cliente',
                'active' => true,
                //cliente
                'tipo' => 'PJ',
                'cep' => '30411-250',
                'telefone' => null,
                'celular' => '(31) 97145-1159',
                'logradouro' => 'Av. Amazonas',
                'numero' => null,
                'complemento' => null,
                'bairro' => 'Prado',
                'id_cidade' => '2310',
                'nome_fantasia' => 'Top Carro BH',
            ],
        ];
        foreach ($clientes as $key => $cliente) {
            $user = User::firstOrCreate(
                ['email' => $cliente['email']],
                [
                    'nome' => $cliente['nome'],
                    'password' => $cliente['password'],
                    'role' => $cliente['role'],
                    'active' => $cliente['active'],
                ]
            );
            $cliente = Cliente::updateOrCreate(
                ['id_usuario' => $user->id],
                [
                    'tipo' => $cliente['tipo'],
                    'cep' => $cliente['cep'],
                    'telefone' => $cliente['telefone'],
                    'celular' => $cliente['celular'],
                    'logradouro' => $cliente['logradouro'],
                    'numero' => $cliente['numero'],
                    'complemento' => $cliente['complemento'],
                    'bairro' => $cliente['bairro'],
                    'id_cidade' => $cliente['id_cidade'],
                    'nome_fantasia' => $cliente['nome_fantasia'],
                ]
            );
        }
    }
}

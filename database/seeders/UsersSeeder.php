<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'nome' => 'André admin',
                'email' => 'andre@teste.com',
                'password' => Hash::make('123'),
                'role' => 'superadmin',
                'active' => true
            ],
            [
                'nome' => 'Alex admin',
                'email' => 'alex@teste.com',
                'password' => Hash::make('123'),
                'role' => 'superadmin',
                'active' => true
            ],
            [
                'nome' => 'Quero Auto admin',
                'email' => 'adm@teste.com',
                'password' => Hash::make('123'),
                'role' => 'superadmin',
                'active' => true
            ],
        ];
        foreach ($usuarios as $key => $usuario) {
            $user = User::firstOrCreate(
                [
                    'email' => $usuario['email'],
                ],
                [
                    'nome' => $usuario['nome'],
                    'password' => $usuario['password'],
                    'role' => $usuario['role'],
                    'active' => $usuario['active'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // AAACadastrosBasicosSeeder::class,
            MunicipiosSeeder::class,
            ModelosSeeder::class,
            // AnunciosSeeder::class,
            FormasPagamentosSeeder::class,
            StatusPagamentosSeeder::class,
            UsersSeeder::class,
            // BannerSeeder::class,
            OpcionaisSeeder::class,
            // PlanosSeeder::class,
            CadastrosBasicosSeeder::class,
            ClienteSeeder::class,
            // LojasSeeder::class,
        ]);
    }
}

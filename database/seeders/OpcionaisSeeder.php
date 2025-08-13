<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Opcional;
use App\Models\Uf;
use Illuminate\Database\Seeder;

class OpcionaisSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $opcionais = [
            'Alarme',
            'Ar condicionado',
            'Freios ABS',
            'Travas Elétricas',
            'Vidros Elétricos',
            'Ar quente',
            'Banco do motorista com ajuste de altura',
            'Teto solar',
            'Blindado',
            'Câmera de Ré',
            'Bancos de Couro',
            'CD / MP3',
            'Central multimídia',
            'Computador de bordo',
            'Controle de estabilidade',
            'Controle de tração',
            'Conversível',
            'Desembaçador',
            'Direção Elétrica',
            'Direção Hidráulica',
            'DVD',
            'EBD',
            'Faróis Auxiliares',
            'Faróis LED',
            'Farol Xenônio',
            'GPS',
            'Limpador traseiro',
            'USB',
            'Piloto automático',
            'Retrovisores Elétricos',
            'Rodas de Liga Leve',
            'Sensor de Estacionamento',
            'Tração 4x4',
            'Turbo',
            'Volante ajustável',
            'Volante com multimídia',
        ];
        foreach ($opcionais as $key => $op) {
            Opcional::updateOrCreate(
                ['descricao' => $op],
                []
            );
        }
    }
}

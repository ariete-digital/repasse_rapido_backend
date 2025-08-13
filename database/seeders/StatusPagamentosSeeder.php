<?php

namespace Database\Seeders;

use App\Models\StatusPagamento;
use Illuminate\Database\Seeder;

class StatusPagamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusPagamento::create([
            'codigo' => 'AGUARDANDO',
            'descricao' => 'Aguardando pagamento'
        ]);

        StatusPagamento::create([
            'codigo' => 'APROVADO',
            'descricao' => 'Aprovado'
        ]);

        StatusPagamento::create([
            'codigo' => 'RECUSADO',
            'descricao' => 'Recusado'
        ]);
    }
}

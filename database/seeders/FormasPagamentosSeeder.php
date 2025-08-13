<?php

namespace Database\Seeders;

use App\Models\FormaPagamento;
use Illuminate\Database\Seeder;

class FormasPagamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FormaPagamento::create([
            'codigo' => 'CREDITO',
            'descricao' => 'Crédito'
        ]);

        FormaPagamento::create([
            'codigo' => 'PIX',
            'descricao' => 'Pix'
        ]);

        FormaPagamento::create([
            'codigo' => 'BOLETO',
            'descricao' => 'Boleto'
        ]);
    }
}

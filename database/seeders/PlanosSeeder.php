<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Plano;
use App\Models\PrecoAnuncioPlano;
use Illuminate\Database\Seeder;

class PlanosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $planos = [
            [
                'nome' => 'Destaque',
                'descricao' => 'Destinado a PESSOAS FÍSICAS e JURÍDICAS. Nesse plano os veículos anunciados terão suas aparições na tela inicial simultaneamente com os demais veículos anunciados, como também poderá ser localizado através da busca personalizada.',
                'tipo' => 'D',
                'descricao_curta' => 'Destinado a pessoas Físicas e Jurídicas, o anúncio aparecerá em destaque na tela inicial.',
            ],
            [
                'nome' => 'Aberto',
                'descricao' => 'Destinado a PESSOAS FÍSICAS e JURÍDICAS. Nesse plano os veículos anunciados serão localizados através da busca personalizada.',
                'tipo' => 'A',
                'descricao_curta' => 'Destinado a Pessoas Físicas e Jurídicas, o Veiculo será  localizado através da busca rápida.',
            ],
            [
                'nome' => 'Fechado',
                'descricao' => 'Destinado apenas a PESSOAS FÍSICAS. Nesse plano o valor do anúncio é equivalente a 50% do valor do "Anúncio Aberto", porém os dados do vendedor estarão ocultos no anúncio e para obtenção dos dados do vendedor, o interessado terá que efetuar o pagamento de 50% do valor do anúncio equivalente ao restante do valor integral do "Anúncio Aberto". Todos os anúncios serão moderados, nesse tipo de anúncio é proibido colocar fotos, imagens ou descrição no anuncio que conduza o interessado compradorao anunciante, caso isso ocorra, as informações serão retiradas do anúncio.',
                'tipo' => 'F',
                'descricao_curta' => 'Destinado somente a Pessoas Físicas, custa 50% do valor do Anúncio Aberto e o comprador pagará o restante do valor para visualizar os dados do vendedor.',
            ],
        ];
        foreach ($planos as $key => $plano) {
            $p = Plano::updateOrCreate(
                [
                    'tipo' => $plano['tipo'],
                ],
                [
                    'nome' => $plano['nome'],
                    'descricao' => $plano['descricao'],
                    'descricao_curta' => $plano['descricao_curta'],
                ]
            );
            for ($i=0; $i < 3; $i++) { 
                $quant = 1;
                $preco = 34.95;
                if($p->tipo == 'A') $preco = 69.9;
                else if($p->tipo == 'D') $preco = 109.9;
                if($i == 1){
                    $quant = 10;
                    $preco = 9 * $preco;
                } 
                else if($i == 2){
                    $quant = 50;
                    $preco = 40 * $preco;
                }
                PrecoAnuncioPlano::updateOrCreate(
                    [
                        'id_plano' => $p->id,
                        'quant_anuncios' => $quant,
                    ],
                    [
                        'preco' => $preco,
                    ]
                );
            }
        }
    }
}

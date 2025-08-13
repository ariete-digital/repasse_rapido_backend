<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Cor;
use App\Models\Faq;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\ParametrosGerais;
use App\Models\TipoCambio;
use App\Models\TipoCombustivel;
use App\Models\TipoParabrisa;
use App\Models\TipoPneu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CadastrosBasicosSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->populaTipoCambio();
        $this->populaTipoCombustivel();
        $this->populaTipoPneu();
        $this->populaTipoParabrisa();
        $this->populaCor();
        $this->populaFaq();
        $this->populaParametroGeral();
    }

    private function populaTipoCambio(): void
    {
        $tipos = [
            [
                'descricao' => 'Manual',
                'tipo_veiculo' => 'C'
            ],
            [
                'descricao' => 'Automático',
                'tipo_veiculo' => 'C'
            ],
            [
                'descricao' => 'Automatizado',
                'tipo_veiculo' => 'C'
            ],
            [
                'descricao' => 'Automático',
                'tipo_veiculo' => 'M'
            ],
            [
                'descricao' => 'Pedal',
                'tipo_veiculo' => 'M'
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoCambio::firstOrCreate(
                [
                    'descricao' => $modelo['descricao'],
                    'tipo_veiculo' => $modelo['tipo_veiculo']
                ],
                []
            );
        }
    }

    private function populaTipoCombustivel(): void
    {
        $tipos = [
            [
                'descricao' => 'Álcool',
            ],
            [
                'descricao' => 'Gasolina',
            ],
            [
                'descricao' => 'Diesel',
            ],
            [
                'descricao' => 'Flex',
            ],
            [
                'descricao' => 'Elétrico',
            ],
            [
                'descricao' => 'Híbrido',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoCombustivel::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaTipoPneu(): void
    {
        $tipos = [
            [
                'descricao' => 'Bons',
            ],
            [
                'descricao' => 'Ruins',
            ],
            [
                'descricao' => 'Meia vida',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoPneu::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaTipoParabrisa(): void
    {
        $tipos = [
            [
                'descricao' => 'Em perfeito estado',
            ],
            [
                'descricao' => 'Trincado',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = TipoParabrisa::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaCor(): void
    {
        $tipos = [
            [
                'descricao' => 'Branco',
            ],
            [
                'descricao' => 'Prata',
            ],
            [
                'descricao' => 'Violeta',
            ],
            [
                'descricao' => 'Vermelho',
            ],
            [
                'descricao' => 'Cinza',
            ],
            [
                'descricao' => 'Preto',
            ],
        ];
        foreach ($tipos as $key => $modelo) {
            $user = Cor::firstOrCreate(
                ['descricao' => $modelo['descricao']],
                []
            );
        }
    }

    private function populaFaq(): void
    {
        $perguntas = [
            [
                'pergunta' => 'Como criar minha conta?',
                'resposta' => 'Para criar sua conta basta clicar no botão “Cadastre-se” localizado no canto superior direito da tela e preencher seus dados.',
            ],
            [
                'pergunta' => 'Como atualizar meu cadastro?',
                'resposta' => 'Para atualizar o seu cadastro basta acessar sua conta clicando no botão “Entrar” localizado no canto superior direto do site. Após realizar o login utilizando seu e-mail e senha, você terá acesso ao Menu clicando no círculo com a letra inicial do seu nome. Para atualizar seu cadastro basta clicar em “Minha Conta” e seus dados serão exibidos. Você poderá preencher as informações de acordo com a sua necessidade. Para alterar, envie uma mensagem através do "Fale conosco" solicitando a alteração dos dados.',
            ],
            [
                'pergunta' => 'Como retirar meu anúncio da plataforma Quero Auto?',
                'resposta' => 'Para retirar seu anúncio da plataforma você deverá acessar sua conta clicando no botão “Entrar” localizado no canto superior direto do site. Após realizar o login utilizando seu e-mail e senha cadastrados, basta acessar a opção “Gerenciar meus anúncios”, disponível no menu lateral ou no círculo com a inicial do seu nome (canto superior direto do site). Serão mostrados os principais dados do anúncio e você terá a opção de: solicitar alteração, pausar/reativar anúncio e encerrar anúncio. Para finalizar o anúncio basta clicar no botão “Encerrar Anúncio”',
            ],
            [
                'pergunta' => 'Tive problemas na negociação, como devo proceder?',
                'resposta' => 'Na página inicial do site Quero Auto existe um serviço que pode ser contratado junto a uma equipe jurídica, que está à disposição dos usuários. Na seção “ESTOU VENDENDO OU COMPRANDO UM VEÍCULO E ESTOU TENDO PROBLEMAS, PRECISO DE AJUDA DE UM ADVOGADO”, na página HOME, clique no botão “Fale com um Advogado” e você terá acesso a uma sugestão quanto à solução do problema por uma pequena taxa (praticamente simbólica). Você poderá escolher como receber a sugestão: por ligação telefônica, por e-mail ou aplicativos de mensagem (opção mais ágil). Caso prefira também poderá contratar uma consulta on-line (valor da tabela OAB).',
            ],
            [
                'pergunta' => 'Como relatar um problema com um anunciante?',
                'resposta' => 'Caso tenha algum problema com um anunciante, relate o mesmo através do SAC / ENVIE UMA MENSAGEM. Relate o ocorrido informando os detalhes do problema e o link do anúncio. Caso haja divergências nas informações do anúncio, iremos entrar em contato com o anunciante para as devidas correções, caso contrário solicitamos seguir as instruções da pergunta "Tive problemas na negociação, como devo proceder?".',
            ],
            [
                'pergunta' => 'Como funcionam os planos de anúncios disponíveis no Quero Auto?',
                'resposta' => 'No Quero Auto você encontra três tipos de planos para realizar o anúncio de veículos. São eles: Anúncio em Destaque: Destinado a PESSOAS FÍSICAS e JURÍDICAS. Nesse plano os veículos anunciados terão suas aparições na tela inicial simultaneamente com os demais veículos anunciados, como também poderá ser localizado através da busca personalizada. Anúncio Aberto: Destinado a PESSOAS FÍSICAS e JURÍDICAS. Nesse plano os veículos anunciados serão localizados através da busca personalizada. Anúncio Fechado: Destinado apenas a PESSOAS FÍSICAS. Nesse plano o valor do anúncio é equivalente a 50% do valor do "Anúncio Aberto", porém os dados do vendedor estarão ocultos no anúncio e para obtenção dos dados do vendedor, o interessado terá que efetuar o pagamento de 50% do valor do anúncio equivalente ao restante do valor integral do "Anúncio Aberto". Todos os anúncios serão moderados e neste tipo de anúncio é proibido colocar fotos, imagens ou descrição no anuncio que conduza o interessado comprador ao anunciante, caso isso ocorra, as informações serão retiradas do anúncio.',
            ],
            [
                'pergunta' => 'Como entrar em contato com o vendedor?',
                'resposta' => 'A- Se o anúncio efetuado for das modalidades “DESTAQUE” ou “ ABERTO” é só clicar no contato do vendedor e após aparecer as “DICAS DE SEGURANÇA”, que aconselhamos ler com muita atenção, são dicas que podem evitar muitas dores de cabeça e problemas na negociação, e o usuário clicar em LI E ENTENDI que já irá ser fornecido o contato do vendedor. B- Se o anuncio efetuado de for da modalidade “ANUNCIO FECHADO”, haverá a informação na tela do veículo anunciado que trata-se de um “ANÚNCIO FECHADO” onde o anunciante só efetuou o pagamento equivalente a 50% ( metade) do valor do anúncio os outros 50% ( outra metade ) quem pagará para obter os dados do vendedor será o interessado comprador. c- Todos os anúncios efetuados por Lojas e Revendas de Veículos, são efetuados na modalidade “ ANÚNCIO ABERTO” com todos os dados do vendedor já disponibilizados após o interessado comprador ler as “DICAS DE SEGURANÇA” ”, que aconselhamos ler com muita atenção, são dicas que podem evitar muitas dores de cabeça e problemas na negociação, e o usuário clicar em LI E ENTENDI que já irá ser fornecido o contato do vendedor.',
            ],
            [
                'pergunta' => 'Como editar meus anúncios?',
                'resposta' => 'Para editar um anúncio, clique no círculo com a inicial do seu nome, no canto superior direito, e em seguida clique em "Meus anúncios". Os anúncios serão listados na tela, então clique em "Gerenciar anúncio" no anúncio que deseja editar. Em seguida, clique em "Alterar informações". O passo a passo para a edição aparecerá em seguida. Obs.: Todas as informações no anúncio são editáveis pelo usuário, exceto: Número de Renavan, Caracteres da Placa, Marca do Veículo e Modelo do Veículo (essas alterações devem ser solicitadas para serem efetuadas através do SAC / ENVIE UMA MENSAGEM)',
            ],
        ];
        foreach ($perguntas as $key => $pergunta) {
            $user = Faq::updateOrCreate(
                ['pergunta' => $pergunta['pergunta']],
                ['resposta' => $pergunta['resposta']]
            );
        }
    }

    private function populaParametroGeral(): void
    {
        $parametros = [
            [
                'chave' => 'VALOR_COMISSAO_MODERADOR',
                'valor' => '5.00',
            ],
        ];
        foreach ($parametros as $key => $param) {
            ParametrosGerais::updateOrCreate(
                ['chave' => $param['chave']],
                ['valor' => $param['valor']]
            );
        }
    }
}

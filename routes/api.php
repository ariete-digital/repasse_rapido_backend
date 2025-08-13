<?php

use App\Http\Controllers\AnuncioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannersController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComissaoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ConfiguracoesController;
use App\Http\Controllers\ContatoController;
use App\Http\Controllers\CorController;
use App\Http\Controllers\EscritorioRegionalController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InatividadeController;
use App\Http\Controllers\IntegracaoNFController;
use App\Http\Controllers\IntegradorController;
use App\Http\Controllers\LojasController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\MeusClientesController;
use App\Http\Controllers\MinhaContaController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\SubregiaoController;
use App\Http\Controllers\TipoCambioController;
use App\Http\Controllers\TipoCombustivelController;
use App\Http\Controllers\TipoParabrisaController;
use App\Http\Controllers\TipoPneuController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VendedorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/cadastrar', [AuthController::class, 'cadastrar'])->name('cadastrar');
Route::post('/recuperar_senha', [AuthController::class, 'recuperarSenha'])->name('recuperar_senha');
Route::post('/cadastrar_nova_senha', [AuthController::class, 'cadastrarNovaSenha'])->name('cadastrar_nova_senha');

Route::prefix('cliente')
    ->as('cliente.')
    ->group(function () {
        Route::get('home', [HomeController::class, 'obterInfo'])->name('home.obter_info');
        Route::post('anuncios/filtrar', [AnuncioController::class, 'filtrar'])->name('anuncios.filtrar');
        Route::get('anuncios/detalhe', [AnuncioController::class, 'detalhe'])->name('anuncios.detalhe');
        Route::get('anuncios/obter_dados_anunciante', [AnuncioController::class, 'obterDadosAnunciante'])->name('anuncios.obter_dados_anunciante');

        Route::get('listagem/modelos', [ModeloController::class, 'listagemModelos'])->name('listagem.modelos');
        Route::get('listagem/todas_cidades', [MinhaContaController::class, 'listagemTodasCidades'])->name('listagem.todas_cidades');
        Route::get('listagem/cidades_cadastradas', [MinhaContaController::class, 'listagemCidadesCadastradas'])->name('listagem.cidades_cadastradas');
        Route::get('listagem/cidades_banners', [MinhaContaController::class, 'listagemCidadesBanners'])->name('listagem.cidades_banners');
        Route::get('listagem/cidades', [MinhaContaController::class, 'listagemCidades'])->name('listagem.cidades');
        Route::get('listagem/estados', [MinhaContaController::class, 'listagemEstados'])->name('listagem.estados');
        Route::get('listagem/estados_cadastrados', [MinhaContaController::class, 'listagemEstadosCadastradas'])->name('listagem.estados_cadastrados');
        Route::get('listagem/marcas', [MarcaController::class, 'listagemMarcas'])->name('listagem.marcas');
        Route::get('listagem/cores', [CorController::class, 'listagemCores'])->name('listagem.cores');
        Route::get('listagem/planos', [PlanoController::class, 'listagemPlanos'])->name('listagem.planos');
        Route::get('listagem/opcionais', [AnuncioController::class, 'listagemOpcionais'])->name('listagem.opcionais');
        Route::get('listagem/tipos_cambio', [AnuncioController::class, 'listagemTiposCambio'])->name('listagem.tipos_cambio');
        Route::get('listagem/tipos_combustivel', [AnuncioController::class, 'listagemTiposCombustivel'])->name('listagem.tipos_combustivel');

        Route::get('obter_cidade', [MinhaContaController::class, 'obterCidade'])->name('obter_cidade');

        Route::get('faq', [FaqController::class, 'obterFaqCompleto'])->name('faq.lista');
        Route::post('contato', [ContatoController::class, 'enviarContato'])->name('contato.enviar');

        Route::get('lojas/filtrar', [LojasController::class, 'lista'])->name('lojas.lista');
        Route::get('lojas/obter', [LojasController::class, 'get'])->name('lojas.getBySlug');
        Route::get('lojas/{id}', [LojasController::class, 'show'])->name('lojas.show');
        Route::get('get_logos_lojas', [LojasController::class, 'getLogos'])->name('lojas.get_logos_lojas');

        Route::get('banners_by_type', [BannersController::class, 'getBannersByType'])->name('banners.get_banners_by_type');
        Route::get('banners', [BannersController::class, 'getBannersByLocation'])->name('banners.get_banners_by_location');

        Route::post('pagamento/notificacao', [PagamentoController::class, 'notificacao'])->name('pagamento.notificacao');
    });

Route::middleware(['auth.basic.api'])->group(function () {
    Route::get('/v1/clientes', [IntegracaoNFController::class, 'obterClientesV1'])->name('integracao_nf.v1.clientes');
    Route::get('/v1/pedidos', [IntegracaoNFController::class, 'obterPedidosV1'])->name('integracao_nf.v1.pedidos');
    Route::get('/v1/comissoes', [IntegracaoNFController::class, 'obterComissoesV1'])->name('integracao_nf.v1.comissoes');
});

Route::middleware(['auth.jwt'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');

    /**
     *   ADMIN
     */
    Route::prefix('admin')
        ->as('admin.')
        ->middleware(['check.role:superadmin,admin,vendedor,gerente'])
        ->group(function () {

            Route::get('planos', [PlanoController::class, 'lista'])->name('planos.lista');
            Route::get('planos/obter', [PlanoController::class, 'obter'])->name('planos.obter');
            Route::post('planos/salvar', [PlanoController::class, 'salvar'])->name('planos.salvar');
            Route::post('planos/excluir', [PlanoController::class, 'excluir'])->name('planos.excluir');

            Route::get('faq', [FaqController::class, 'lista'])->name('faq.lista');
            Route::get('faq/obter', [FaqController::class, 'obter'])->name('faq.obter');
            Route::post('faq/salvar', [FaqController::class, 'salvar'])->name('faq.salvar');
            Route::post('faq/excluir', [FaqController::class, 'excluir'])->name('faq.excluir');

            Route::get('cores', [CorController::class, 'lista'])->name('cores.lista');
            Route::get('cores/obter', [CorController::class, 'obter'])->name('cores.obter');
            Route::post('cores/salvar', [CorController::class, 'salvar'])->name('cores.salvar');
            Route::post('cores/excluir', [CorController::class, 'excluir'])->name('cores.excluir');

            Route::get('tipos_parabrisa', [TipoParabrisaController::class, 'lista'])->name('tipos_parabrisa.lista');
            Route::get('tipos_parabrisa/obter', [TipoParabrisaController::class, 'obter'])->name('tipos_parabrisa.obter');
            Route::post('tipos_parabrisa/salvar', [TipoParabrisaController::class, 'salvar'])->name('tipos_parabrisa.salvar');
            Route::post('tipos_parabrisa/excluir', [TipoParabrisaController::class, 'excluir'])->name('tipos_parabrisa.excluir');

            Route::get('tipos_combustivel', [TipoCombustivelController::class, 'lista'])->name('tipos_combustivel.lista');
            Route::get('tipos_combustivel/obter', [TipoCombustivelController::class, 'obter'])->name('tipos_combustivel.obter');
            Route::post('tipos_combustivel/salvar', [TipoCombustivelController::class, 'salvar'])->name('tipos_combustivel.salvar');
            Route::post('tipos_combustivel/excluir', [TipoCombustivelController::class, 'excluir'])->name('tipos_combustivel.excluir');

            Route::get('tipos_cambio', [TipoCambioController::class, 'lista'])->name('tipos_cambio.lista');
            Route::get('tipos_cambio/obter', [TipoCambioController::class, 'obter'])->name('tipos_cambio.obter');
            Route::post('tipos_cambio/salvar', [TipoCambioController::class, 'salvar'])->name('tipos_cambio.salvar');
            Route::post('tipos_cambio/excluir', [TipoCambioController::class, 'excluir'])->name('tipos_cambio.excluir');

            Route::get('tipos_pneu', [TipoPneuController::class, 'lista'])->name('tipos_pneu.lista');
            Route::get('tipos_pneu/obter', [TipoPneuController::class, 'obter'])->name('tipos_pneu.obter');
            Route::post('tipos_pneu/salvar', [TipoPneuController::class, 'salvar'])->name('tipos_pneu.salvar');
            Route::post('tipos_pneu/excluir', [TipoPneuController::class, 'excluir'])->name('tipos_pneu.excluir');

            Route::get('escritorios', [EscritorioRegionalController::class, 'lista'])->name('escritorios.lista');
            Route::get('escritorios/obter', [EscritorioRegionalController::class, 'obter'])->name('escritorios.obter');
            Route::get('escritorios/obter_detalhes', [EscritorioRegionalController::class, 'obterDetalhes'])->name('escritorios.obter_detalhes');
            Route::post('escritorios/salvar', [EscritorioRegionalController::class, 'salvar'])->name('escritorios.salvar');
            Route::post('escritorios/excluir', [EscritorioRegionalController::class, 'excluir'])->name('escritorios.excluir');
            Route::get('escritorios/listagemInfo', [EscritorioRegionalController::class, 'listagemInfo'])->name('escritorios.listagem_info');

            Route::get('subregioes', [SubregiaoController::class, 'lista'])->name('subregioes.lista');
            Route::get('subregioes/obter', [SubregiaoController::class, 'obter'])->name('subregioes.obter');
            Route::get('subregioes/obter_detalhes', [SubregiaoController::class, 'obterDetalhes'])->name('subregioes.obter_detalhes');
            Route::post('subregioes/salvar', [SubregiaoController::class, 'salvar'])->name('subregioes.salvar');
            Route::post('subregioes/excluir', [SubregiaoController::class, 'excluir'])->name('subregioes.excluir');
            Route::get('subregioes/listagemInfo', [SubregiaoController::class, 'listagemInfo'])->name('subregioes.listagem_info');

            Route::get('usuarios', [UsuarioController::class, 'lista'])->name('usuarios.lista');
            Route::get('usuarios/obter', [UsuarioController::class, 'obter'])->name('usuarios.obter');
            Route::post('usuarios/salvar', [UsuarioController::class, 'salvar'])->name('usuarios.salvar');
            Route::post('usuarios/excluir', [UsuarioController::class, 'excluir'])->name('usuarios.excluir');
            Route::post('usuarios/status', [UsuarioController::class, 'alterarStatus'])->name('usuarios.alterarStatus');
            Route::get('listagem/perfis_usuario', [UsuarioController::class, 'listagemPerfis'])->name('usuarios.perfis_usuario');

            Route::get('marcas', [MarcaController::class, 'lista'])->name('marcas.lista');
            Route::get('marcas/obter', [MarcaController::class, 'obter'])->name('marcas.obter');
            Route::post('marcas/salvar', [MarcaController::class, 'salvar'])->name('marcas.salvar');
            Route::post('marcas/excluir', [MarcaController::class, 'excluir'])->name('marcas.excluir');

            Route::get('modelos', [ModeloController::class, 'lista'])->name('modelos.lista');
            Route::get('modelos/obter', [ModeloController::class, 'obter'])->name('modelos.obter');
            Route::post('modelos/salvar', [ModeloController::class, 'salvar'])->name('modelos.salvar');
            Route::post('modelos/excluir', [ModeloController::class, 'excluir'])->name('modelos.excluir');

            Route::get('moderacao', [AnuncioController::class, 'listaModeracao'])->name('moderacao.lista');
            Route::get('moderacao/detalhe', [AnuncioController::class, 'detalheModeracao'])->name('moderacao.detalhe');
            Route::post('moderacao/salvar', [AnuncioController::class, 'salvarModeracao'])->name('moderacao.salvar');
            Route::get('moderacao/download', [AnuncioController::class, 'download'])->name('moderacao.download');

            Route::get('banners', [BannersController::class, 'getBanners'])->name('banners.get_banners');
            Route::get('banners/obter', [BannersController::class, 'obterBanner'])->name('banners.obter');
            Route::post('banners/salvar', [BannersController::class, 'saveBanner'])->name('banners.salvar');
            Route::post('banners/excluir', [BannersController::class, 'deleteBanner'])->name('banners.excluir');
            Route::post('banners/duplicar', [BannersController::class, 'duplicateBanner'])->name('banners.duplicar');

            Route::get('pedidos', [PedidoController::class, 'lista'])->name('pedidos.lista');
            Route::get('pedidos/detalhe', [PedidoController::class, 'obterDetalhePedido'])->name('pedidos.detalhe');

            Route::get('minhas_comissoes', [ComissaoController::class, 'listaMinhasComissoes'])->name('minhas_comissoes.lista');
            Route::get('usuarios_comissoes', [ComissaoController::class, 'listaUsuarios'])->name('comissoes.usuarios_comissoes');
            Route::get('comissoes', [ComissaoController::class, 'listaComissoes'])->name('comissoes.lista');

            Route::get('anuncios', [AnuncioController::class, 'adminLista'])->name('anuncios.lista');
            Route::get('anuncios/detalhe', [AnuncioController::class, 'adminObterDetalhe'])->name('anuncios.detalhe');
            Route::post('anuncios/excluir', [AnuncioController::class, 'excluirAnuncio'])->name('anuncios.excluirAnuncio');
            Route::post('anuncios/toogle_destaque', [AnuncioController::class, 'toogleDestaque'])->name('anuncios.toogleDestaque');

            Route::get('meus_clientes', [MeusClientesController::class, 'lista'])->name('meus_clientes.lista');
            Route::get('meus_clientes/obter', [MeusClientesController::class, 'obter'])->name('meus_clientes.obter');

            Route::get('vendedores', [VendedorController::class, 'lista'])->name('vendedores.lista');
            Route::get('vendedores/obter', [VendedorController::class, 'obter'])->name('vendedores.obter');
            Route::post('vendedores/salvar', [VendedorController::class, 'salvar'])->name('vendedores.salvar');
            Route::post('vendedores/excluir', [VendedorController::class, 'excluir'])->name('vendedores.excluir');
            Route::post('vendedores/status', [VendedorController::class, 'alterarStatus'])->name('vendedores.alterarStatus');

            Route::get('meu_escritorio/obter', [EscritorioRegionalController::class, 'obterMeuEscritorio'])->name('meu_escritorio.obter');

            Route::get('planos', [PlanoController::class, 'lista'])->name('planos.lista');
            Route::get('planos/obter', [PlanoController::class, 'obter'])->name('planos.obter');
            Route::post('planos/salvar', [PlanoController::class, 'salvar'])->name('planos.salvar');
            Route::post('planos/excluir', [PlanoController::class, 'excluir'])->name('planos.excluir');

            Route::get('configuracoes/obter', [ConfiguracoesController::class, 'obter'])->name('configuracoes.obter');
            Route::post('configuracoes/salvar', [ConfiguracoesController::class, 'salvar'])->name('configuracoes.salvar');

            Route::get('minha_conta/obter', [MinhaContaController::class, 'adminObter'])->name('minha_conta.obter');

            Route::get('clientes', [ClienteController::class, 'lista'])->name('clientes.lista');
            Route::get('clientes/obter', [ClienteController::class, 'obter'])->name('clientes.obter');
            Route::get('clientes/obter_detalhes', [ClienteController::class, 'obterDetalhes'])->name('clientes.obter_detalhes');
            Route::post('clientes/salvar', [ClienteController::class, 'salvar'])->name('clientes.salvar');
            Route::get('clientes/executar_integracao', [ClienteController::class, 'executarIntegracao'])->name('clientes.executar_integracao');
            Route::get('clientes/obter_id_integracao', [ClienteController::class, 'obterIdIntegracao'])->name('clientes.obter_id_integracao');
            Route::get('clientes/verificar_status_integracao', [ClienteController::class, 'verificarStatus'])->name('clientes.verificar_status_integracao');

            Route::get('inatividade/obter_info', [InatividadeController::class, 'obterInfo'])->name('inatividade.obter_info');
            Route::get('inatividade/representantes', [InatividadeController::class, 'obterListaUsuarios'])->name('inatividade.lista_usuarios');
        });

    /**
     * CLIENTE
     */
    Route::prefix('cliente')
        ->as('cliente.')
        ->middleware(['check.role:cliente'])
        ->group(function () {
            Route::get('meus_anuncios', [AnuncioController::class, 'meusAnuncios'])->name('meus_anuncios.lista');
            Route::get('meus_anuncios/obter_info', [AnuncioController::class, 'obterInfo'])->name('meus_anuncios.obter_info');
            Route::post('meus_anuncios/salvar', [AnuncioController::class, 'salvar'])->name('meus_anuncios.salvar');
            Route::post('meus_anuncios/excluir', [AnuncioController::class, 'excluir'])->name('meus_anuncios.excluir');
            Route::get('meus_anuncios/obter_info_pagamento', [CompraController::class, 'obterInfoPagamentoCriacaoAnuncio'])->name('meus_anuncios.obter_info_pagamento');
            Route::post('meus_anuncios/excluir', [AnuncioController::class, 'excluirRascunho'])->name('meus_anuncios.excluirRascunho');
            Route::post('meus_anuncios/pagamento', [CompraController::class, 'fazerPagamentoCriacaoAnuncio'])->name('meus_anuncios.pagamento');
            Route::get('meus_anuncios/detalhes', [AnuncioController::class, 'detalhesMeuAnucio'])->name('meus_anuncios.detalhes');
            Route::post('meus_anuncios/pausar', [AnuncioController::class, 'tooglePausar'])->name('meus_anuncios.pausar');
            Route::post('meus_anuncios/encerrar', [AnuncioController::class, 'encerrar'])->name('meus_anuncios.encerrar');
            Route::post('meus_anuncios/renovar', [AnuncioController::class, 'renovar'])->name('meus_anuncios.renovar');
            Route::post('meus_anuncios/solicitar_alteracao', [AnuncioController::class, 'solicitarAlteracao'])->name('meus_anuncios.solicitar_alteracao');
            Route::post('meus_anuncios/processar_alteracao', [AnuncioController::class, 'processarAlteracao'])->name('meus_anuncios.processar_alteracao');
            Route::get('meus_anuncios/obter_licencas', [AnuncioController::class, 'obterLicencas'])->name('meus_anuncios.obter_licencas');
            Route::post('meus_anuncios/gerar_anuncio', [AnuncioController::class, 'gerarAnuncioPJ'])->name('meus_anuncios.gerar_anuncio');
            Route::post('meus_anuncios/criar_rascunho_repasse', [AnuncioController::class, 'criarRascunhoRepasse'])->name('meus_anuncios.criar_rascunho_repasse');
            Route::post('meus_anuncios/obter_info_veiculo', [AnuncioController::class, 'obterInfoVeiculo'])->name('meus_anuncios.obter_info_veiculo');
            Route::post('meus_anuncios/comprar_destaque', [AnuncioController::class, 'comprarDestaque'])->name('meus_anuncios.comprar_destaque');
            Route::get('meus_anuncios/obter_info_pagamento_extra', [CompraController::class, 'obterInfoPagamentoExtra'])->name('meus_anuncios.obter_info_pagamento_extra');
            Route::post('meus_anuncios/pagamento_extra', [CompraController::class, 'fazerPagamentoExtra'])->name('meus_anuncios.pagamento_extra');

            // Route::get('pagamentos/obter_info', [PagamentoController::class, 'obterInfo'])->name('pagamentos.obter_info');
            // Route::post('pagamentos/salvar', [PagamentoController::class, 'fazerPagamento'])->name('pagamentos.salvar');

            Route::get('minha_conta/obter', [MinhaContaController::class, 'obter'])->name('minha_conta.obter');
            Route::post('minha_conta/salvar', [MinhaContaController::class, 'salvar'])->name('minha_conta.salvar');

            Route::post('compra_plano/salvar', [CompraController::class, 'salvarPlanoAnuncio'])->name('compra_plano.salvar');
            Route::get('compra_plano/obter_info', [CompraController::class, 'obterInfoPlanoAnuncio'])->name('compra_plano.obter_info');
            Route::post('compra_plano/pagamento', [CompraController::class, 'pagarPlanoAnuncio'])->name('compra_plano.pagamento');

            Route::get('faq', [FaqController::class, 'obterFaqCompleto'])->name('faq.lista');
            Route::post('contato', [ContatoController::class, 'enviarContato'])->name('contato.enviar');

            Route::get('minhas_compras/obter', [CompraController::class, 'obterMinhasCompras'])->name('minhas_compras.lista');
            Route::get('minhas_compras/obter_detalhes', [CompraController::class, 'obterDetalhesMinhasCompras'])->name('minhas_compras.detalhes_compra');

            Route::post('anuncios/salvar_aceite', [AnuncioController::class, 'salvarAceite'])->name('anuncios.salvar_aceite');
            Route::get('anuncios/obter_info_pagamento', [CompraController::class, 'obterInfoPagamentoAnuncioFechado'])->name('anuncios.obter_info_pagamento');
            Route::post('anuncios/salvar_pagamento', [CompraController::class, 'salvarPagamentoAnuncioFechado'])->name('anuncios.salvar_pagamento');
            Route::get('anuncios/verificar_pedido', [AnuncioController::class, 'verificarPedido'])->name('anuncios.verificar_pedido');

            Route::post('notificacao_repasse', [ContatoController::class, 'enviarNotificacaoRepasse'])->name('notificacao_repasse.enviar');
        });
});

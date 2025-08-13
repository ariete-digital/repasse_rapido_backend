<?php

namespace App\Http\Controllers;

use App\Helpers\AnuncioHelper;
use App\Helpers\ApiPlacasHelper;
use App\Helpers\AuthHelper;
use App\Helpers\Base64Helper;
use App\Helpers\ClienteHelper;
use App\Helpers\ComissaoHelper;
use App\Helpers\DtoHelper;
use App\Helpers\ModeracaoHelper;
use App\Helpers\WhatsappHelper;
use App\Models\AceiteTermoCliente;
use App\Models\Anuncio;
use App\Models\AnuncioRascunho;
use App\Models\CarrinhoCompra;
use App\Models\Cliente;
use App\Models\Cor;
use App\Models\ImagensAnuncio;
use App\Models\ImagensRascunho;
use App\Models\LicencaAnuncio;
use App\Models\Marca;
use App\Models\OpcionaisRascunho;
use App\Models\Opcional;
use App\Models\Pedido;
use App\Models\Plano;
use App\Models\TipoCambio;
use App\Models\TipoCombustivel;
use App\Models\TipoParabrisa;
use App\Models\TipoPneu;
use App\Services\AnunciosService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnuncioController extends Controller
{
    const NUM_REG_POR_PAG = 30;

    public function __construct(private readonly AnunciosService $anunciosService)
    {
    }

    public function filtrar(Request $request)
    {
        Log::info($request->all());
        // Log::info('tipos_cambio');
        // Log::info($request->tipos_cambio);
        $limit = $request->has('limit') ? (int) $request->input('limit') : null;
        $page = $request->has('page') ? (int) $request->input('page') : null;
        $offset = (!is_null($limit) && !is_null($page)) ? ($page - 1) * $limit : null;
        $anuncios = $this
            ->anunciosService
            ->getAnuncios(
                tipoVeiculo: $request->tipo_veiculo ?? null,
                tipoVenda: $request->tipo_venda ?? null,
                idCidade: $request->id_cidade ?? null,
                idEstado: $request->id_estado ?? null,
                idMarca: $request->id_marca ?? null,
                idModelo: $request->id_modelo ?? null,
                versaoVeiculo: $request->versao_veiculo ?? null,
                statusVeiculo: $request->status_veiculo ?? null,
                anoMin: $request->ano['min'] ?? null,
                anoMax: $request->ano['max'] ?? null,
                valorMin: $request->valor['min'] ?? null,
                valorMax: $request->valor['max'] ?? null,
                quilometragemMin: $request->quilometragem['min'] ?? null,
                quilometragemMax: $request->quilometragem['max'] ?? null,
                tiposVendedor: $request->tipos_vendedor ?? null,
                tiposCambio: $request->tipos_cambio ?? null,
                tiposCombustivel: $request->tipos_combustivel ?? null,
                numPortas: $request->num_portas ?? null,
                idCor: $request->cor ?? null,
                opcionais: $request->opcionais ?? null,
                idCliente:  $request->id_cliente ?? $request->id_loja ?? null,
                slug:  $request->slug ?? null,
                ordenacao: $request->ordenacao ?? null,
                nomeMarca: $request->marca ?? null,
                nomeModelo: $request->modelo ?? null,
                limit: $limit,
                offset: $offset,
            )
        ;

        // Log::info($anuncios);
        $anunciosDTO = DtoHelper::getListaAnunciosDTO(collect($anuncios['items']), false, true, true);

        //retornar opcionais, tipo combustivel e tipo cambio
        $tiposCombustivel = TipoCombustivel::get();
        $listaTiposCombustivelDTO = DtoHelper::getListaItemSimplesDTO($tiposCombustivel);

        $tiposCambio = TipoCambio::get();
        $listaTiposCambioDTO = DtoHelper::getListaItemSimplesDTO($tiposCambio);

        $opcionais = Opcional::whereNull('id_usuario');
        if(Auth::check()){
            $opcionais = $opcionais->orWhere('id_usuario', Auth::id());
        }
        $opcionais = $opcionais->get();
        $listaOpcionaisDTO = DtoHelper::getListaOpcionaisDTO($opcionais, null);

        return $this->getResponse('success', [
            'anuncios' => $anunciosDTO,
            'total' => $anuncios['total'],
            'listaTiposCombustivel' => $listaTiposCombustivelDTO,
            'listaTiposCambio' => $listaTiposCambioDTO,
            'listaOpcionais' => $listaOpcionaisDTO,
        ]);
    }

    public function detalhe(Request $request)
    {
        $anuncio = Anuncio::where('codigo', $request->codigo)
            ->with('cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa', 'opcionais', 'cliente.usuario')
            ->first();

        // Log::info($anuncio);
        if($anuncio->pausado || !$anuncio->ativo || !$anuncio->cliente->usuario->active){
            return $this->getResponse('redirect', []);
        }

        $anuncioDTO = DtoHelper::getAnuncioDTO($anuncio, false, true, false, true);
        unset($anuncioDTO['cliente']);

        $aceitouTermos = false;
        $existePedido = false;
        if(Auth::check()){
            $cliente = Cliente::where('id_usuario', Auth::id())->first();
            $aceiteTermos = AceiteTermoCliente::where('id_cliente', $cliente->id)->where('id_anuncio', $anuncio->id)->first();
            $aceitouTermos = $aceiteTermos && $aceiteTermos->aceite_termos_condicoes == 1 && $aceiteTermos->aceite_anuncio_fechado;

            if($anuncio->tipo_plano == 'F'){
                $existePedido = Pedido::where('id_cliente', $cliente->id)
                    ->where('id_anuncio', $anuncio->id)
                    ->whereRelation('pagamento.statusPagamento', 'codigo', '=', 'APROVADO')
                    ->exists();
            }
        }

        $anuncio->num_cliques = $anuncio->num_cliques + 1;
        $anuncio->save();

        $anuncianteDTO = DtoHelper::getInfoAnuncianteDTO($anuncio->cliente);

        // Log::info(json_encode([
        //     'anuncio->tipo' => $anuncio->tipo_plano,
        //     'exibeContato' => $exibeContato,
        //     'existePedido' => $existePedido,
        // ]));

        return $this->getResponse('success', [
            // 'anuncioPuro' => $anuncio,
            'anuncio' => $anuncioDTO,
            'aceitouTermos' => $aceitouTermos,
            'existePedido' => $existePedido,
            'anunciante' => $anuncianteDTO,
        ]);
    }

    public function meusAnuncios(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $anunciosEmCriacao = AnuncioRascunho::where('id_cliente', $cliente->id)->whereNull('id_anuncio_original')->get();
        $anunciosEdicao = AnuncioRascunho::where('id_cliente', $cliente->id)->whereNotNull('id_anuncio_original')->get();
        $anunciosPublicados = Anuncio::where('id_cliente', $cliente->id)->get();

        $anunciosEmCriacaoDTO = DtoHelper::getListaAnunciosDTO($anunciosEmCriacao, true, true, true);
        $anunciosEdicaoDTO = DtoHelper::getListaAnunciosDTO($anunciosEdicao, true, true, true);
        $anunciosPublicadosDTO = DtoHelper::getListaAnunciosDTO($anunciosPublicados, false, true, true);

        $licencaAnuncio = LicencaAnuncio::where('id_cliente', $cliente->id)->first();
        $licencaAnuncioDTO = DtoHelper::getLicencaAnuncioDTO($licencaAnuncio);

        return $this->getResponse('success', [
            'anunciosEmCriacao' => $anunciosEmCriacaoDTO,
            'anunciosEdicao' => $anunciosEdicaoDTO,
            'anunciosPublicados' => $anunciosPublicadosDTO,
            'licencaAnuncio' => $licencaAnuncioDTO,
            'isPJ' => $cliente->tipo == 'PJ',
        ]);
    }

    public function obterInfo(Request $request)
    {
        $anuncioRascunho = null;
        $anuncioRascunhoDTO = null;
        if($request->id_anuncio_rascunho){
            $anuncioRascunho = AnuncioRascunho::where('id', $request->id_anuncio_rascunho)
                ->with('cliente', 'cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa', 'imagens')
                ->first();
            // Log::info($anuncioRascunho);
            $anuncioRascunhoDTO = DtoHelper::getAnuncioDTO($anuncioRascunho, true, true);
        }

        $marcas = Marca::with(['modelos' => function($query){
            $query->orderBy('descricao');
        }])->orderBy('descricao')->get();
        $marcasDTO = DtoHelper::getListaMarcasDTO($marcas);

        $cores = Cor::get();
        $coresDTO = DtoHelper::getListaItemSimplesDTO($cores);

        $tiposCambio = TipoCambio::get();
        $tiposCambioDTO = DtoHelper::getListaTiposCambioDTO($tiposCambio);

        $tiposCombustivel = TipoCombustivel::get();
        $tiposCombustivelDTO = DtoHelper::getListaItemSimplesDTO($tiposCombustivel);

        $tiposParabrisa = TipoParabrisa::get();
        $tiposParabrisaDTO = DtoHelper::getListaItemSimplesDTO($tiposParabrisa);

        $tiposPneu = TipoPneu::get();
        $tiposPneuDTO = DtoHelper::getListaItemSimplesDTO($tiposPneu);

        $planos = Plano::where('tipo', 'A')->with('precos')->get();
        $planosDTO = DtoHelper::getListaPlanosDTO($planos);

        $opcionais = Opcional::where('id_usuario', Auth::id())->orwhereNull('id_usuario')->get();
        $opcionaisRascunho = OpcionaisRascunho::where('id_anuncio_rascunho', $request->id_anuncio_rascunho)->get();
        $opcionaisDTO = DtoHelper::getListaOpcionaisDTO($opcionais, $opcionaisRascunho);
        $opcionaisSelecionados = $opcionaisRascunho->pluck('id_opcional');

        $imagens = AnuncioHelper::obterImagensRascunho($anuncioRascunho);

        $isMinhaContaCompleta = ClienteHelper::isMinhaContaCompleta();
        $isPJ = false;
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        if($cliente->tipo == 'PJ'){
            $isPJ = true;
        }
        $licencaAnuncio = LicencaAnuncio::where('id_cliente', $cliente->id)->first();

        return $this->getResponse('success', [
            'anuncioRascunho' => $anuncioRascunhoDTO,
            'marcas' => $marcasDTO,
            'cores' => $coresDTO,
            'tiposCambio' => $tiposCambioDTO,
            'tiposCombustivel' => $tiposCombustivelDTO,
            'tiposParabrisa' => $tiposParabrisaDTO,
            'tiposPneu' => $tiposPneuDTO,
            'opcionais' => $opcionaisDTO,
            'opcionaisSelecionados' => $opcionaisSelecionados,
            'planos' => $planosDTO,
            'isMinhaContaCompleta' => $isMinhaContaCompleta,
            'imagens' => $imagens,
            'isPJ' => $isPJ,
        ]);
    }

    public function salvar(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $tipoVendedor = $this->getTipoVendedor($cliente);
        if($request->id){
            $anuncioRascunho = AnuncioRascunho::where('id', $request->id)->first();
        } else {
            $anuncioRascunho = new AnuncioRascunho();
            $anuncioRascunho->tipo_venda = 'C';
        }
        // Log::info(json_encode([
        //     'request' => $request->all(),
        // ]));
        $anuncioRascunho->id_cliente = $cliente->id;
        $anuncioRascunho->tipo_vendedor = $tipoVendedor;
        if($request->tipo_plano){
            $anuncioRascunho->tipo_plano = $request->tipo_plano;
        }
        // if($request->id_modelo){
        //     $anuncioRascunho->id_modelo = $request->id_modelo;
        // }
        if($request->id_cor){
            $anuncioRascunho->id_cor = $request->id_cor;
        }
        if($request->id_tipo_cambio){
            $anuncioRascunho->id_tipo_cambio = $request->id_tipo_cambio;
        }
        if($request->id_tipo_combustivel){
            $anuncioRascunho->id_tipo_combustivel = $request->id_tipo_combustivel;
        }
        if($request->id_tipo_pneu){
            $anuncioRascunho->id_tipo_pneu = $request->id_tipo_pneu;
        }
        if($request->id_tipo_parabrisa){
            $anuncioRascunho->id_tipo_parabrisa = $request->id_tipo_parabrisa;
        }
        if($request->tipo_veiculo){
            $anuncioRascunho->tipo_veiculo = $request->tipo_veiculo;
        }
        if($request->marca_veiculo){
            $anuncioRascunho->marca_veiculo = $request->marca_veiculo;
        }
        if($request->modelo_veiculo){
            $anuncioRascunho->modelo_veiculo = $request->modelo_veiculo;
        }
        if($request->submodelo){
            $anuncioRascunho->submodelo = $request->submodelo;
        }
        if($request->valor_fipe){
            $anuncioRascunho->valor_fipe = $request->valor_fipe;
        }
        if($request->renavam){
            $anuncioRascunho->renavam = $request->renavam;
        }
        if($request->placa){
            $anuncioRascunho->placa = $request->placa;
        }
        if($request->status_veiculo){
            $anuncioRascunho->status_veiculo = $request->status_veiculo;
        }
        if($request->versao_veiculo){
            $anuncioRascunho->versao_veiculo = $request->versao_veiculo;
        }
        if($request->ano_fabricacao){
            $anuncioRascunho->ano_fabricacao = $request->ano_fabricacao;
        }
        if($request->ano_modelo){
            $anuncioRascunho->ano_modelo = $request->ano_modelo;
        }
        if(isset($request->quilometragem)){
            $anuncioRascunho->quilometragem = $request->quilometragem;
        }
        if($request->num_portas){
            $anuncioRascunho->num_portas = $request->num_portas;
        }
        if($request->tipo_motor){
            $anuncioRascunho->tipo_motor = $request->tipo_motor;
        }
        if($request->refrigeracao){
            $anuncioRascunho->refrigeracao = $request->refrigeracao;
        }
        if($request->cilindrada){
            $anuncioRascunho->cilindrada = $request->cilindrada;
        }
        if($request->partida){
            $anuncioRascunho->partida = $request->partida;
        }
        if($request->freios){
            $anuncioRascunho->freios = $request->freios;
        }
        if($request->tipo_freio){
            $anuncioRascunho->tipo_freio = $request->tipo_freio;
        }
        if($request->alarme){
            $anuncioRascunho->alarme = $request->alarme;
        }
        if($request->alimentacao){
            $anuncioRascunho->alimentacao = $request->alimentacao;
        }
        if($request->controle_estabilidade){
            $anuncioRascunho->controle_estabilidade = $request->controle_estabilidade;
        }
        if($request->roda_liga){
            $anuncioRascunho->roda_liga = $request->roda_liga;
        }
        if(isset($request->unico_dono)){
            $anuncioRascunho->unico_dono = $request->unico_dono;
        }
        if(isset($request->tipo_troca)){
            $anuncioRascunho->tipo_troca = $request->tipo_troca;
        }
        if(isset($request->ipva_pago)){
            $anuncioRascunho->ipva_pago = $request->ipva_pago;
        }
        if(isset($request->veiculo_nome_anunciante)){
            $anuncioRascunho->veiculo_nome_anunciante = $request->veiculo_nome_anunciante;
        }
        if(isset($request->financiado)){
            $anuncioRascunho->financiado = $request->financiado;
        }
        if(isset($request->parcelas_em_dia)){
            $anuncioRascunho->parcelas_em_dia = $request->parcelas_em_dia;
        }
        if(isset($request->aceita_financiamento)){
            $anuncioRascunho->aceita_financiamento = $request->aceita_financiamento;
        }
        if(isset($request->todas_revisoes_concessionaria)){
            $anuncioRascunho->todas_revisoes_concessionaria = $request->todas_revisoes_concessionaria;
        }
        if(isset($request->passou_leilao)){
            $anuncioRascunho->passou_leilao = $request->passou_leilao;
        }
        if(isset($request->possui_manual)){
            $anuncioRascunho->possui_manual = $request->possui_manual;
        }
        if(isset($request->possui_chave_reserva)){
            $anuncioRascunho->possui_chave_reserva = $request->possui_chave_reserva;
        }
        if(isset($request->possui_ar)){
            $anuncioRascunho->possui_ar = $request->possui_ar;
        }
        if(isset($request->ar_funcionando)){
            $anuncioRascunho->ar_funcionando = $request->ar_funcionando;
        }
        if(isset($request->escapamento_solta_fumaca)){
            $anuncioRascunho->escapamento_solta_fumaca = $request->escapamento_solta_fumaca;
        }
        if(isset($request->garantia_fabrica)){
            $anuncioRascunho->garantia_fabrica = $request->garantia_fabrica;
        }
        if(isset($request->motor_bate)){
            $anuncioRascunho->motor_bate = $request->motor_bate;
        }
        if(isset($request->cambio_faz_barulho)){
            $anuncioRascunho->cambio_faz_barulho = $request->cambio_faz_barulho;
        }
        if(isset($request->cambio_escapa_marcha)){
            $anuncioRascunho->cambio_escapa_marcha = $request->cambio_escapa_marcha;
        }
        if(isset($request->luz_injecao)){
            $anuncioRascunho->luz_injecao = $request->luz_injecao;
        }
        if(isset($request->luz_airbag)){
            $anuncioRascunho->luz_airbag = $request->luz_airbag;
        }
        if(isset($request->luz_abs)){
            $anuncioRascunho->luz_abs = $request->luz_abs;
        }
        if(isset($request->tipo_monta)){
            $anuncioRascunho->tipo_monta = $request->tipo_monta;
        }
        if(isset($request->furtado_roubado)){
            $anuncioRascunho->furtado_roubado = $request->furtado_roubado;
        }
        if($request->valor){
            $valor = $request->valor;
            if(str_contains($valor,',')){
                $valor = str_replace('.','', $valor);
                $valor = str_replace(',','.', $valor);
            }
            $anuncioRascunho->valor = $valor;
        }
        if($request->descricao){
            $anuncioRascunho->descricao = $request->descricao;
        }
        if(isset($request->aceite_termos)){
            $anuncioRascunho->aceite_termos = $request->aceite_termos == "true";
        }
        if($request->id_plano){
            $anuncioRascunho->id_plano = $request->id_plano;
        }
        $anuncioRascunho->save();

        if($request->opcionais){
            OpcionaisRascunho::where('id', $anuncioRascunho->id)->whereNotIn('id_opcional', $request->opcionais)->delete();
            foreach ($request->opcionais as $key => $idOpcional) {
                OpcionaisRascunho::updateOrCreate(
                    [
                        'id_opcional' => $idOpcional,
                        'id_anuncio_rascunho' => $anuncioRascunho->id,
                    ],
                    []
                );
            }
        }

        if(gettype($request->idsRemovidos) == 'string'){
            $idsArray = explode(',', $request->idsRemovidos);
            ImagensRascunho::whereIn('id', $idsArray)->delete();
        }
        if($request->imagens){
            $basePath = ImagensRascunho::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncioRascunho->id;
            Storage::makeDirectory($basePath);
            foreach ($request->imagens as $key => $imagem) {
                if($imagem != "undefined"){
                    $filePath = Storage::putFileAs(
                        $basePath,
                        $imagem,
                        $imagem->getClientOriginalName(),
                        'public'
                    );
                    // Log::info(json_encode([
                    //     'filePath' => $filePath,
                    // ]));
                    $imgRasc = ImagensRascunho::create([
                        'id_anuncio_rascunho' => $anuncioRascunho->id,
                        'arquivo' => $imagem->getClientOriginalName(),
                        'principal'=> $key == 0,
                    ]);
                }
            }
        }

        $imagens = AnuncioHelper::obterImagensRascunho($anuncioRascunho);

        return $this->getResponse('success', [
            'id_anuncio_rascunho' => $anuncioRascunho->id,
            'imagens' => $imagens
        ]);
    }

    public function excluirRascunho(Request $request)
    {
        $anuncioRascunho = AnuncioRascunho::where('id', $request->id)->first();
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        if($anuncioRascunho->id_cliente == $cliente->id){
            $anuncioRascunho->delete();
        }

        return $this->getResponse('success', [
            'message' => "Anúncio excluído com sucesso!"
        ]);
    }

    public function toogleAtivo(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id_anuncio)->first();
        $anuncio->ativo = !$anuncio->ativo;

        $status = $anuncio->ativo ? 'ativado' : 'pausado';

        return $this->getResponse('success', [
            'message' => "Anúncio " . $status . "com sucesso!"
        ]);
    }

    public function listaModeracao(Request $request)
    {
        $anuncios = Anuncio::whereNull('moderacao_aprovada')
            ->with('cliente', 'cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa', 'opcionais', 'imagens')
            ->orderBy('created_at', 'desc')
            ->get();

        $anunciosDTO = DtoHelper::getListaAnunciosDTO($anuncios, false, true);
        return $this->getResponse('success', [
            'anuncios' => $anunciosDTO
        ]);
    }

    public function detalheModeracao(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)
            ->with('cliente', 'cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa', 'opcionais', 'imagens')
            ->first();

        $anuncioDTO = DtoHelper::getAnuncioDTO($anuncio, false, true, true, true, true);
        return $this->getResponse('success', [
            'anuncio' => $anuncioDTO
        ]);
    }

    public function salvarModeracao(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id_anuncio)->with('cliente.usuario')->first();
        $anuncio->moderacao_aprovada = $request->moderacao_aprovada == true;
        $anuncio->obs_moderacao = $request->obs_moderacao;
        $anuncio->moderado_em = Carbon::now();
        $anuncio->id_usuario_moderacao = Auth::id();
        $anuncio->save();

        // Log::info(json_encode($anuncio));

        if($anuncio->moderacao_aprovada == false){
            ModeracaoHelper::criarHistoricoModeracao($anuncio);
            ModeracaoHelper::enviarEmailModeracao($anuncio->cliente->usuario->email, $anuncio->cliente->usuario->nome, $anuncio->obs_moderacao);
            $msg = "Olá, ".$anuncio->cliente->usuario->nome.".\nSeu anúncio foi pausado devido a divergências encontradas, solicitamos entrar em seu anúncio na página inicial, efetuar seu login com sua senha, clicar no círculo azul redondo no canto superior direito com a letra inicial de seu nome,  clicar em GERENCIAR MEUS ANÚNCIOS,  clicar no botão azul ALTERAR INFORMAÇÕES, corrigir as divergências e publicar novamente seu anúncio, informamos que o mesmo será novamente moderado e não havendo mais divergências o anúncio será publicado em definitivo, para outras dúvidas clique no Fale Conosco na tela inicial, segue abaixo as divergências encontradas:\n\n".$anuncio->obs_moderacao;
            WhatsappHelper::enviarMensagem($anuncio->cliente->celular, $msg, $anuncio->cliente->id_usuario);
        } else {
            // ComissaoHelper::salvarComissaoModeracao($anuncio->id, $anuncio->tipo_plano);
        }

        return $this->getResponse('success', [
            'message' => 'Moderação salva com sucesso!'
        ]);
    }

    public function detalhesMeuAnucio(Request $request)
    {
        $anuncio = Anuncio::where('codigo', $request->codigo)
            ->with('cliente', 'cor', 'tipoCambio', 'tipoCombustivel', 'tipoPneu', 'tipoParabrisa')
            ->first();
        $isProprietario = AuthHelper::verificaPropriedadeAnuncio($anuncio);
        if(!$isProprietario){
            return $this->getResponse('unauthorized', [
                'message' => 'Acesso não autorizado. Entre em contato com o administrador do sistema.'
            ], 401);
        }
        
        $pedido = Pedido::where('id_anuncio', $anuncio->id)->with('pagamento')->first();
        $permiteDespausar = false;
        if(!$pedido) $permiteDespausar = true;
        if($pedido && $pedido->pagamento->statusPagamento->codigo == "APROVADO") $permiteDespausar = true;

        $anuncioDTO = DtoHelper::getAnuncioDTO($anuncio, false, true);
        $licencaAnuncio = LicencaAnuncio::where('id_cliente', $anuncio->id_cliente)->first();

        $imagens = [];
        if($anuncio){
            $basePath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
            foreach ($anuncio->imagens as $key => $imagem) {
                if(str_starts_with($imagem->arquivo, 'https')){
                    $urlImg = $imagem->arquivo;
                } else {
                    if(Config::get('app.env') == 'production'){
                        $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $imagem->arquivo;
                    } else {
                        $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                    }
                }
                $array = [
                    'id' => $imagem->id,
                    'arquivo' => $imagem->arquivo,
                    'str_base64' => $urlImg,
                    'principal' => $key == 0,
                ];

                array_push($imagens, $array);
                // $file = Storage::get($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                // Log::info(json_encode([
                //     'basePath' => $basePath,
                //     'base64' => $base64,
                //     // 'file' => $file,
                //     'arquivo' => $imagem->arquivo,
                // ]));
            }
        }

        return $this->getResponse('success', [
            'anuncio' => $anuncioDTO,
            'imagens' => $imagens,
            'permiteDespausar' => $permiteDespausar,
            'isLicencaVencida' => $licencaAnuncio ? $licencaAnuncio->is_vencida : false
        ]);
    }

    public function tooglePausar(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)->with('cliente')->first();
        $isProprietario = AuthHelper::verificaPropriedadeAnuncio($anuncio);
        if(!$isProprietario){
            return $this->getResponse('unauthorized', [
                'message' => 'Acesso não autorizado. Entre em contato com o administrador do sistema.'
            ], 401);
        }

        $pedido = Pedido::where('id_anuncio', $anuncio->id)->with('pagamento')->first();
        $permiteDespausar = false;
        if(!$pedido) $permiteDespausar = true;
        if($pedido && $pedido->pagamento->statusPagamento->codigo == "APROVADO") $permiteDespausar = true;

        if($permiteDespausar){
            $anuncio->pausado = !$anuncio->pausado;
            $anuncio->save();
        }

        $licenca = LicencaAnuncio::whereRelation('cliente.usuario', 'id_usuario', '=', Auth::id())->where('data_vencimento', '>', Carbon::now())->first();
        $acao = 'retomado';
        if($anuncio->pausado){
            $acao = 'pausado';
            if($anuncio->cliente->tipo == 'PJ' && $licenca && $licenca->num_licencas != -1){
                $licenca->num_licencas = $licenca->num_licencas + 1;
            }
        } else if($anuncio->cliente->tipo == 'PJ' && $licenca && $licenca->num_licencas != -1){
            $licenca->num_licencas = $licenca->num_licencas - 1;
        }
        if($licenca) $licenca->save();

        return $this->getResponse('success', [
            'message' => 'Anúncio '.$acao.' com sucesso!'
        ]);
    }

    public function encerrar(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)->with('cliente')->first();
        $isProprietario = AuthHelper::verificaPropriedadeAnuncio($anuncio);
        if(!$isProprietario){
            return $this->getResponse('unauthorized', [
                'message' => 'Acesso não autorizado. Entre em contato com o administrador do sistema.'
            ], 401);
        }

        $anuncio->ativo = false;
        $anuncio->save();

        $licenca = LicencaAnuncio::whereRelation('cliente.usuario', 'id_usuario', '=', Auth::id())->where('data_vencimento', '>', Carbon::now())->first();
        if($anuncio->cliente->tipo == 'PJ' && $licenca && $licenca->num_licencas != -1){
            $licenca->num_licencas = $licenca->num_licencas + 1;
            $licenca->save();
        }

        return $this->getResponse('success', [
            'message' => 'Anúncio encerrado com sucesso!'
        ]);
    }

    public function renovar(Request $request)
    {
        $planoAberto = Plano::where('tipo', 'A')->first();
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $carrinhoCompra = CarrinhoCompra::updateOrCreate(
            [
                'id_cliente' => $cliente->id
            ],
            [
                'id_plano' => $planoAberto->id,
                'quant_anuncios' => 1,
                'id_anuncio' => $request->id,
                'info_extra' => 'RENOVACAO',
            ]
        );

        return $this->getResponse('success', []);
    }

    public function solicitarAlteracao(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)
            ->with('opcionais', 'imagens')
            ->first();

        $anuncioArray = $anuncio->toArray();
        unset($anuncioArray['opcionais']);
        unset($anuncioArray['imagens']);
        $anuncioArray['id_anuncio_original'] = $anuncio->id;

        $anuncioRascunho = AnuncioRascunho::create($anuncioArray);
        foreach ($anuncio->opcionais as $key => $opcional) {
            $opcional = OpcionaisRascunho::create([
                'id_opcional' => $opcional->id,
                'id_anuncio_rascunho' => $anuncioRascunho->id,
            ]);
        }
        $oldPath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
        $newPath = ImagensRascunho::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncioRascunho->id;
        foreach ($anuncio->imagens as $key => $imagem) {
            $imagem = ImagensRascunho::create([
                'id_anuncio_rascunho' => $anuncioRascunho->id,
                'arquivo' => $imagem->arquivo,
                'principal' => $imagem->principal,
            ]);
            $oldPathFile = $oldPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            $newPathFile = $newPath . DIRECTORY_SEPARATOR . $imagem->arquivo;
            Storage::copy($oldPathFile,$newPathFile);
        }

        return $this->getResponse('success', [
            'id_anuncio_rascunho' => $anuncioRascunho->id
        ]);
    }

    public function processarAlteracao(Request $request)
    {
        $anuncioRascunho = AnuncioRascunho::where('id', $request->id_anuncio_rascunho)
            ->with('opcionais', 'imagens')
            ->first();

        $anuncioOriginal = Anuncio::where('id', $anuncioRascunho->id_anuncio_original)->first();

        $novoAnuncio = AnuncioHelper::gerarAnuncioDoRascunho($anuncioRascunho, $anuncioOriginal->id);
        $novoAnuncio->moderacao_aprovada = 1;
        $novoAnuncio->moderado_em = null;
        $novoAnuncio->obs_moderacao = null;
        $novoAnuncio->id_usuario_moderacao = null;
        $novoAnuncio->save();

        return $this->getResponse('success', []);
    }

    public function obterLicencas(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $licencas = LicencaAnuncio::where('id_cliente', $cliente->id)
            ->where('num_licencas', '>', 0)
            ->get();
        if($cliente->tipo == 'PJ' && (count($licencas) == 0 || (count($licencas) > 0 && $licencas[0]->is_vencida))){
            return $this->getResponse('success', [
                'redirectComprarPlano' => true,
            ]);
        }
        $licencasDTO = DtoHelper::getListaLicencasDTO($licencas);
        return $this->getResponse('success', [
            'licencas' => $licencasDTO,
        ]);
    }

    public function gerarAnuncioPJ(Request $request)
    {
        $anuncioRascunho = AnuncioRascunho::where('id', $request->id_anuncio_rascunho)
            ->with('opcionais', 'imagens')
            ->first();

        // Log::info(json_encode([
        //     'anuncioRascunho' => $anuncioRascunho,
        // ]));
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $licencaAnuncio = LicencaAnuncio::where('id_cliente', $cliente->id)->first();
        // Log::info(json_encode([
        //     'licencaAnuncio' => $licencaAnuncio,
        // ]));
        if($licencaAnuncio && $licencaAnuncio->num_licencas > 0 && !$licencaAnuncio->is_vencida){
            $anuncioRascunho->tipo_plano = $licencaAnuncio->tipo_plano;
            $anuncio = AnuncioHelper::gerarAnuncioDoRascunho($anuncioRascunho);

            $licencaAnuncio->num_licencas = $licencaAnuncio->num_licencas - 1;
            $licencaAnuncio->save();
        } else {
            return $this->getResponse('success', [
                'error' => true,
                'message' => 'Você não possui mais licenças disponíveis no seu plano. Para prosseguir, libere uma licença ou faça um upgrade de plano.'
            ]);
        }

        return $this->getResponse('success', [
            'message' => 'Anúncio publicado com sucesso!'
        ]);
    }

    public function salvarAceite(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $aceite = AceiteTermoCliente::updateOrCreate(
            [
                'id_cliente' => $cliente->id,
                'id_anuncio' => $request->id_anuncio,
            ],
            [
                'aceite_termos_condicoes' => $request->aceite_termos_condicoes,
                'aceite_anuncio_fechado' => $request->aceite_anuncio_fechado,
            ]
        );

        return $this->getResponse('success', []);
    }

    public function verificarPedido(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $existePedido = Pedido::where('id_cliente', $cliente->id)->where('id_anuncio', $request->id_anuncio)->exists();

        return $this->getResponse('success', [
            'existePedido' => $existePedido
        ]);
    }

    public function obterDadosAnunciante(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id_anuncio)
            ->with('cliente.usuario', 'cliente.cidade.estado')
            ->first();

        $exibeContato = false;
        if(Auth::check()){
            $cliente = Cliente::where('id_usuario', Auth::id())->first();
            $existePedido = false;
            if($anuncio->tipo_plano == 'F'){
                $existePedido = Pedido::where('id_cliente', $cliente->id)->where('id_anuncio', $anuncio->id)->exists();
                if($existePedido){
                    $exibeContato = true;
                }
            } else {
                $exibeContato = true;
            }
        } else if($anuncio->tipo_plano != 'F') {
            $exibeContato = true;
        }

        if($exibeContato){
            $anuncianteDTO = DtoHelper::getInfoAnuncianteDTO($anuncio->cliente);
            return $this->getResponse('success', [
                'anunciante' => $anuncianteDTO
            ]);
        } else {
            return $this->getResponse('unauthorized', [], 401);
        }
    }

    public function listagemModelos(Request $request){
        $modelos = Modelo::where(function($query) use ($request){
            if ($request->id_marca) {
                $query->where('id_marca', $request->id_marca);
            }
            if ($request->filtro) {
                $query->where('descricao', 'LIKE', '%'.$request->filtro.'%');
            }
        })
        ->limit(20)
        ->get();

        $modelosDTO = DtoHelper::getListagemAsyncSelectDTO($modelos);
        return $this->getResponse('success', $modelosDTO);
    }

    public function listagemOpcionais(Request $request)
    {
        // Log::info('listagemOpcionais');
        $opcionais = Opcional::whereNull('id_usuario');
        if(Auth::check()){
            $opcionais = $opcionais->where('id_usuario', Auth::id());
        }
        $opcionais = $opcionais->get();
        $listaOpcionaisDTO = DtoHelper::getListaOpcionaisDTO($opcionais, null);
        return $this->getResponse('success', [
            'listaOpcionais' => $listaOpcionaisDTO,
        ]);
    }

    public function listagemTiposCambio(Request $request)
    {
        $tiposCambio = TipoCambio::get();
        $listaTiposCambioDTO = DtoHelper::getListaItemSimplesDTO($tiposCambio);
        return $this->getResponse('success', [
            'listaTiposCambio' => $listaTiposCambioDTO,
        ]);
    }

    public function listagemTiposCombustivel(Request $request)
    {
        $tiposCombustivel = TipoCombustivel::get();
        $listaTiposCombustivelDTO = DtoHelper::getListaItemSimplesDTO($tiposCombustivel);
        return $this->getResponse('success', [
            'listaTiposCombustivel' => $listaTiposCombustivelDTO,
        ]);
    }

    public function criarRascunhoRepasse(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $licencas = LicencaAnuncio::where('id_cliente', $cliente->id)
            ->where('tipo_plano', 'A')
            ->where('num_licencas', '>', 0)
            ->get();

        if($cliente->tipo == 'PJ' && count($licencas) == 0){
            return $this->getResponse('success', [
                'redirectComprarPlano' => true,
                'tipoPlanoFiltro' => 'A',
            ]);
        }

        $idAnuncioRascunho = AnuncioHelper::criarRascunhoRepasse($cliente->id);

        return $this->getResponse('success', [
            'id_anuncio_rascunho' => $idAnuncioRascunho
        ]);
    }

    public function adminLista(Request $request)
    {
        $paginacao = new Anuncio();
        if($request->id_cliente) {
            $paginacao = $paginacao->where('id_cliente', $request->id_cliente);
        }
        if($request->tipo_anuncio != "TODOS") {
            if($request->tipo_anuncio == "ATIVOS"){
                $paginacao = $paginacao->where('ativo', true);
            } else if($request->tipo_anuncio == "PAUSADOS"){
                $paginacao = $paginacao->where('pausado', true);
            } else if($request->tipo_anuncio == "ENCERRADOS"){
                $paginacao = $paginacao->where('ativo', false);
            } 
        }
        if ($request->filtro) {
            $paginacao = $paginacao->where('codigo', 'LIKE', '%' . $request->filtro . '%')
                ->orWhereRelation('cliente.usuario', 'nome', 'LIKE', '%' . $request->filtro . '%');
        }
        $paginacao = $paginacao->orderBy('created_at','desc')
            ->paginate(AnuncioController::NUM_REG_POR_PAG);
        $anuncios = $paginacao->items();
        $listaAnunciosDTO = DtoHelper::getListaAnunciosDTO($anuncios, false, true);
        return $this->getResponse('success', [
            'anuncios' => $anuncios,
            'listaAnuncios' => $listaAnunciosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }

    public function adminObterDetalhe(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)->with('usuarioModeracao')->first();

        $imagens = [];
        if($anuncio){
            $basePath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
            foreach ($anuncio->imagens as $key => $imagem) {
                // $base64 = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                $urlCdn = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $imagem->arquivo;

                $array = [
                    'id' => $imagem->id,
                    'arquivo' => $imagem->arquivo,
                    'str_base64' => $urlCdn,
                    'principal' => $key == 0,
                ];

                array_push($imagens, $array);
            }
        }

        return $this->getResponse('success', [
            'anuncio' => DtoHelper::getAnuncioDTO($anuncio, false, true),
            'imagens' => $imagens
        ]);
    }

    public function download(Request $request)
    {
        $cliente = Cliente::where('id', $request->id_cliente)->first();
        $url = '';
        if($request->arquivo == 'cnh')
            $url = $cliente->url_cnh;
        else if($request->arquivo == 'comp')
            $url = $cliente->url_comprovante;
        else if($request->arquivo == 'doc')
            $url = $cliente->url_doc_complementar;

        // Log::info(json_encode([
        //     'url' => $url,
        //     'cliente' => $cliente,
        //     'request' => $request->all(),
        // ]));
        return Storage::download($url);
    }

    public function excluirAnuncio(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)->delete();
        return $this->getResponse('success', [
            'message' => "Anúncio excluído com sucesso!"
        ]);
    }

    public function toogleDestaque(Request $request)
    {
        $anuncio = Anuncio::where('id', $request->id)->first();
        if($anuncio->tipo_plano == 'A'){
            $anuncio->tipo_plano = 'D';
            $acao = 'aplicado';
        }
        else if($anuncio->tipo_plano == 'D'){
            $anuncio->tipo_plano = 'A';
            $acao = 'removido';
        }
        $anuncio->save();
        return $this->getResponse('success', [
            'message' => "Destaque $acao com sucesso!"
        ]);
    }

    public function obterInfoVeiculo(Request $request)
    {
        $retorno = ApiPlacasHelper::buscarInfoVeiculo($request->placa);
        // Log::info($retorno);

        $valorFipe = null;
        if($retorno['fipe'] && $retorno['fipe']['dados']){
            $melhorFipe = $retorno['fipe']['dados'][0];
            foreach ($retorno['fipe']['dados'] as $key => $dado) {
                if($dado['score'] > $melhorFipe['score']){
                    $melhorFipe = $dado;
                }
            }
            $valorFipe = $melhorFipe['texto_valor'];
        }

        return $this->getResponse('success', [
            // 'id_anuncio_rascunho' => $anuncioRascunho->id,
            'marca' => $retorno['MARCA'],
            'modelo' => $retorno['MODELO'],
            'submodelo' => $retorno['SUBMODELO'],
            'ano_fabricacao' => $retorno['ano'],
            'ano_modelo' => $retorno['anoModelo'],
            'valor_fipe' => $valorFipe,
        ]);
    }

    public function comprarDestaque(Request $request)
    {
        $planoDestaque = Plano::where('tipo', 'D')->first();
        $cliente = Cliente::where('id_usuario', Auth::id())->first();
        $carrinhoCompra = CarrinhoCompra::updateOrCreate(
            [
                'id_cliente' => $cliente->id
            ],
            [
                'id_plano' => $planoDestaque->id,
                'quant_anuncios' => 1,
                'id_anuncio' => $request->id,
                'info_extra' => 'DESTAQUE',
            ]
        );

        return $this->getResponse('success', []);
    }

    /**
     *
     * FUNCOES UTILITARIAS
     *
     */

    public function getTipoVendedor($cliente)
    {
        if($cliente->tipo == 'PF') return 'P';
        if($cliente->tipo == 'PJ') return 'R';
        return '';
    }

    public function getTipoVenda($cliente, $tipoVenda)
    {
        if($cliente->tipo == 'PF') return 'C';
        if($cliente->tipo == 'PJ'){
            if(!$tipoVenda) return 'C';
            return $tipoVenda;
        }
        return '';
    }
}

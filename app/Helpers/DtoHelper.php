<?php

namespace App\Helpers;

use App\Models\Cliente;
use App\Models\ImagensAnuncio;
use App\Models\ImagensRascunho;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DtoHelper
{
    public static function getListaUsuariosDTO($usuarios, $mostraSubregiao = false)
    {
        $listaUsuariosDTO = [];
        foreach ($usuarios as $key => $usuario) {
            $usuarioDTO = DtoHelper::getUsuarioDTO($usuario, $mostraSubregiao);
            array_push($listaUsuariosDTO, $usuarioDTO);
        }
        return $listaUsuariosDTO;
    }

    public static function getUsuarioDTO($usuario, $mostraSubregiao = false)
    {
        $usuarioDTO = [
            'id' => $usuario->id,
            'nome' => $usuario->nome,
            'email' => $usuario->email,
            'endereco' => $usuario->endereco,
            'telefone' => $usuario->telefone,
            'nome_banco' => $usuario->nome_banco,
            'num_agencia' => $usuario->num_agencia,
            'num_conta' => $usuario->num_conta,
            'inscricao_estadual' => $usuario->inscricao_estadual,
            'cnpj' => $usuario->cnpj,
            'percentual_comissao' => $usuario->percentual_comissao,
            'role' => $usuario->role,
            'role_str' => $usuario->role_str,
            'active' => $usuario->active == 1,
            'active_str' => $usuario->active_str,
        ];
        if($mostraSubregiao && $usuario->subregiao){
            $usuarioDTO['subregiao'] = [
                'id' => $usuario->subregiao->id,
                'nome' => $usuario->subregiao->nome,
            ];
        }
        return $usuarioDTO;
    }

    public static function getListaEscritoriosDTO($escritorios, $exibeUfs = false)
    {
        $listaEscritoriosDTO = [];
        foreach ($escritorios as $key => $escritorio) {
            $escritorioDTO = DtoHelper::getEscritorioDTO($escritorio, $exibeUfs);
            array_push($listaEscritoriosDTO, $escritorioDTO);
        }
        return $listaEscritoriosDTO;
    }

    public static function getEscritorioDTO($escritorio, $exibeUfs = false)
    {
        $escritorioDTO = [
            'id' => $escritorio->id,
            'nome' => $escritorio->nome,
            'endereco' => $escritorio->endereco,
            'email' => $escritorio->email,
            'telefone' => $escritorio->telefone,
            'percentual_comissao' => $escritorio->percentual_comissao,
            'percentual_comissao_str' => $escritorio->percentual_comissao_str,
            'num_ufs' => $escritorio->ufs->count(),
        ];
        if($escritorio->usuario){
            $escritorioDTO['usuario'] = [
                'id' => $escritorio->usuario->id,
                'nome' => $escritorio->usuario->nome,
                'email' => $escritorio->usuario->email,
                'telefone' => $escritorio->usuario->telefone,
                'endereco' => $escritorio->usuario->endereco,
            ];
        }
        if($exibeUfs){
            $listaUfsDTO = [];
            foreach ($escritorio->ufs as $key => $uf) {
                $ufDTO = [
                    'id' => $uf->id,
                    'sigla' => $uf->sigla,
                    'nome' => $uf->nome,
                ];
                array_push($listaUfsDTO, $ufDTO);
            }
            $escritorioDTO['ufs'] = $listaUfsDTO;
        }
        return $escritorioDTO;
    }

    public static function getListaSubregioesDTO($subregioes, $exibeUfs = false, $exibeFaixasCep = false)
    {
        $listaSubregioesDTO = [];
        foreach ($subregioes as $key => $subregiao) {
            $subregiaoDTO = DtoHelper::getSubregiaoDTO($subregiao, $exibeUfs, $exibeFaixasCep);
            array_push($listaSubregioesDTO, $subregiaoDTO);
        }
        return $listaSubregioesDTO;
    }

    public static function getSubregiaoDTO($subregiao, $exibeUfs = false, $exibeFaixasCep = false)
    {
        $subregiaoDTO = [
            'id' => $subregiao->id,
            'nome' => $subregiao->nome,
            'endereco' => $subregiao->endereco,
            'email' => $subregiao->email,
            'telefone' => $subregiao->telefone,
            'percentual_comissao' => $subregiao->percentual_comissao,
            'percentual_comissao_str' => $subregiao->percentual_comissao_str,
            'id_escritorio_regional' => $subregiao->id_escritorio_regional,
        ];
        if($subregiao->usuario){
            $subregiaoDTO['usuario'] = [
                'id' => $subregiao->usuario->id,
                'nome' => $subregiao->usuario->nome,
                'email' => $subregiao->usuario->email,
                'telefone' => $subregiao->usuario->telefone,
                'endereco' => $subregiao->usuario->endereco,
            ];
        }
        if($subregiao->escritorio){
            $subregiaoDTO['escritorio'] = [
                'id' => $subregiao->escritorio->id,
                'nome' => $subregiao->escritorio->nome,
            ];
        }
        if($exibeUfs){
            $listaUfsDTO = [];
            foreach ($subregiao->ufs as $key => $uf) {
                $ufDTO = [
                    'id' => $uf->id,
                    'sigla' => $uf->sigla,
                    'nome' => $uf->nome,
                ];
                array_push($listaUfsDTO, $ufDTO);
            }
            $subregiaoDTO['ufs'] = $listaUfsDTO;
        }
        if($exibeFaixasCep){
            $listaFaixasCepDTO = [];
            foreach ($subregiao->faixasCep as $key => $faixa) {
                $faixaCepDTO = [
                    'cep_inicial' => $faixa->cep_inicial,
                    'cep_final' => $faixa->cep_final,
                ];
                array_push($listaFaixasCepDTO, $faixaCepDTO);
            }
            $subregiaoDTO['faixas_cep'] = $listaFaixasCepDTO;
        }
        return $subregiaoDTO;
    }

    public static function getListaTiposCambioDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'descricao' => $item->descricao,
                'tipo_veiculo' => $item->tipo_veiculo,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListaItemSimplesDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'descricao' => $item->descricao,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListaModelosDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'descricao' => $item->descricao,
                'marca' => [
                    'id' => $item->marca->id,
                    'descricao' => $item->marca->descricao,
                ]
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getModeloDTO($item)
    {
        $itemDTO = [
            'id' => $item->id,
            'descricao' => $item->descricao,
            'marca' => [
                'id' => $item->marca->id,
                'descricao' => $item->marca->descricao,
            ]
        ];
        if($item->marca){
            $itemDTO = [
                ...$itemDTO,
                'marca' => [
                    'id' => $item->marca->id,
                    'descricao' => $item->marca->descricao,
                ]
            ];
        }
        return $itemDTO;
    }

    public static function getListaMarcasDTO($marcas)
    {
        $listaMarcasDTO = [];
        foreach ($marcas as $key => $marca) {
            $marcaDTO = [
                'id' => $marca->id,
                'descricao' => $marca->descricao,
                'tipo_veiculo' => $marca->tipo_veiculo,
            ];
            $listaModelosDTO = [];
            foreach ($marca->modelos as $key => $modelo) {
                $modeloDTO = [
                    'id' => $modelo->id,
                    'descricao' => $modelo->descricao,
                ];
                array_push($listaModelosDTO, $modeloDTO);
            }
            $marcaDTO['modelos'] = $listaModelosDTO;
            array_push($listaMarcasDTO, $marcaDTO);
        }
        return $listaMarcasDTO;
    }

    public static function getListaOpcionaisDTO($opcionais, $opcionaisRascunho)
    {
        $listaOpcionaisDTO = [];
        foreach ($opcionais as $key => $opcional) {
            $opcionalDTO = [
                'id' => $opcional->id,
                'descricao' => $opcional->descricao,
                // 'selecionado' => DtoHelper::isInList($opcionaisRascunho, $opcional)
            ];
            array_push($listaOpcionaisDTO, $opcionalDTO);
        }
        return $listaOpcionaisDTO;
    }

    private static function isInList($lista, $opcional)
    {
        foreach ($lista as $key => $item) {
            if($item->id == $opcional->id) return true;
        }
        return false;
    }

    public static function getListaPlanosDTO($planos)
    {
        $listaPlanosDTO = [];
        foreach ($planos as $key => $plano) {
            $planoDTO = DtoHelper::getPlanoDTO($plano);
            array_push($listaPlanosDTO, $planoDTO);
        }
        return $listaPlanosDTO;
    }

    public static function getPlanoDTO($plano)
    {
        $planoDTO = [
            'id' => $plano->id,
            'nome' => $plano->nome,
            'descricao' => $plano->descricao,
            'descricao_curta' => $plano->descricao_curta,
            'tipo' => $plano->tipo,
        ];
        $listaPrecosDTO = [];
        foreach ($plano->precos as $key => $preco) {
            $precoDTO = [
                'id' => $preco->id,
                'quant_anuncios' => $preco->quant_anuncios,
                'preco' => $preco->preco,
            ];
            array_push($listaPrecosDTO, $precoDTO);
        }
        $planoDTO['precos'] = $listaPrecosDTO;
        return $planoDTO;
    }

    public static function getListaAnunciosDTO($anuncios, $isRascunho = false, $incluirRelacionamentos = false, $incluirImagemPrincipal = false, $incluirTodasImagens = false, $inclurDownloads = false)
    {
        $listaAnunciosDTO = [];
        foreach ($anuncios as $key => $anuncio) {
            $anuncioDTO = DtoHelper::getAnuncioDTO($anuncio, $isRascunho, $incluirRelacionamentos, $incluirImagemPrincipal, $incluirTodasImagens, $inclurDownloads);
            array_push($listaAnunciosDTO, $anuncioDTO);
        }
        return $listaAnunciosDTO;
    }

    public static function getAnuncioDTO($anuncio, $isRascunho = false, $incluirRelacionamentos = false, $incluirImagemPrincipal = false, $incluirTodasImagens = false, $inclurDownloads = false)
    {
        $anuncioDTO = [
            'id' => $anuncio->id,
            'tipo_plano' => $anuncio->tipo_plano,
            'tipo_plano_str' => $anuncio->tipo_plano_str,
            'tipo_venda' => $anuncio->tipo_venda,
            'tipo_venda_str' => $anuncio->tipo_venda_str,
            'tipo_vendedor' => $anuncio->tipo_vendedor,
            'tipo_vendedor_str' => $anuncio->tipo_vendedor_str,
            'tipo_veiculo' => $anuncio->tipo_veiculo,
            'tipo_veiculo_str' => $anuncio->tipo_veiculo_str,
            'marca_veiculo' => $anuncio->marca_veiculo,
            'modelo_veiculo' => $anuncio->modelo_veiculo,
            'submodelo' => $anuncio->submodelo,
            'valor_fipe' => $anuncio->valor_fipe,
            'renavam' => $anuncio->renavam,
            'placa' => $anuncio->placa,
            'status_veiculo' => $anuncio->status_veiculo,
            // 'versao_veiculo' => $anuncio->versao_veiculo,
            'ano_fabricacao' => $anuncio->ano_fabricacao,
            'ano_modelo' => $anuncio->ano_modelo,
            'quilometragem' => $anuncio->quilometragem,
            'num_portas' => $anuncio->num_portas,
            'tipo_motor' => $anuncio->tipo_motor,
            'tipo_motor_str' => $anuncio->tipo_motor_str,
            'refrigeracao' => $anuncio->refrigeracao,
            'refrigeracao_str' => $anuncio->refrigeracao_str,
            'cilindrada' => $anuncio->cilindrada,
            'cilindrada_str' => $anuncio->cilindrada_str,
            'partida' => $anuncio->partida,
            'partida_str' => $anuncio->partida_str,
            'freios' => $anuncio->freios,
            'freios_str' => $anuncio->freios_str,
            'tipo_freio' => $anuncio->tipo_freio,
            'tipo_freio_str' => $anuncio->tipo_freio_str,
            'alimentacao' => $anuncio->alimentacao,
            'alimentacao_str' => $anuncio->alimentacao_str,
            'alarme' => $anuncio->alarme,
            'controle_estabilidade' => $anuncio->controle_estabilidade,
            'roda_liga' => $anuncio->roda_liga,
            'unico_dono' => $anuncio->unico_dono,
            'unico_dono_str' => $anuncio->unico_dono_str,
            'tipo_troca' => $anuncio->tipo_troca,
            'tipo_troca_str' => $anuncio->tipo_troca_str,
            'ipva_pago' => $anuncio->ipva_pago,
            'veiculo_nome_anunciante' => $anuncio->veiculo_nome_anunciante,
            'financiado' => $anuncio->financiado,
            'parcelas_em_dia' => $anuncio->parcelas_em_dia,
            'aceita_financiamento' => $anuncio->aceita_financiamento,
            'todas_revisoes_concessionaria' => $anuncio->todas_revisoes_concessionaria,
            'passou_leilao' => $anuncio->passou_leilao,
            'possui_manual' => $anuncio->possui_manual,
            'possui_chave_reserva' => $anuncio->possui_chave_reserva,
            'possui_ar' => $anuncio->possui_ar,
            'ar_funcionando' => $anuncio->ar_funcionando,
            'escapamento_solta_fumaca' => $anuncio->escapamento_solta_fumaca,
            'garantia_fabrica' => $anuncio->garantia_fabrica,
            'motor_bate' => $anuncio->motor_bate,
            'cambio_faz_barulho' => $anuncio->cambio_faz_barulho,
            'cambio_escapa_marcha' => $anuncio->cambio_escapa_marcha,
            'luz_injecao' => $anuncio->luz_injecao,
            'luz_airbag' => $anuncio->luz_airbag,
            'luz_abs' => $anuncio->luz_abs,
            'tipo_monta' => $anuncio->tipo_monta,
            'tipo_monta_str' => $anuncio->tipo_monta_str,
            'furtado_roubado' => $anuncio->furtado_roubado,
            'valor' => $anuncio->valor,
            'descricao' => $anuncio->descricao,
            'aceite_termos' => $anuncio->aceite_termos,
            'id_cliente' => $anuncio->id_cliente,
            'id_plano' => $anuncio->id_plano,
            'id_modelo' => $anuncio->id_modelo,
            'id_cor' => $anuncio->id_cor,
            'id_tipo_cambio' => $anuncio->id_tipo_cambio,
            'id_tipo_combustivel' => $anuncio->id_tipo_combustivel,
            'id_tipo_pneu' => $anuncio->id_tipo_pneu,
            'id_tipo_parabrisa' => $anuncio->id_tipo_parabrisa,
            'status_str' => $anuncio->status_str,
            'moderacao_str' => $anuncio->moderacao_str,
            'moderacao_aprovada' => true,
            'num_cliques' => $anuncio->num_cliques,
            'obs_moderacao' => $anuncio->obs_moderacao,
            'created_at' => $anuncio->created_at->format('d/m/Y'),
        ];
        if($isRascunho){
            $anuncioDTO = [
                ...$anuncioDTO,
                'id_anuncio_original' => $anuncio->id_anuncio_original,
            ];
        } else {
            $anuncioDTO = [
                ...$anuncioDTO,
                'ativo' => $anuncio->ativo == 1,
                'pausado' => $anuncio->pausado == 1,
                'codigo' => $anuncio->codigo,
                'is_vencido' => $anuncio->is_vencido,
            ];
        }
        if($incluirRelacionamentos){
            if($anuncio->cliente) {
                $anuncioDTO['cliente'] = [
                    'id' => $anuncio->cliente->id,
                    'nome' => $anuncio->cliente->usuario->nome,
                    'num_documento' => $anuncio->cliente->num_documento,
                    'isPJ' => $anuncio->cliente->tipo == 'PJ',
                    'telefone' => $anuncio->cliente->telefone,
                    'celular' => $anuncio->cliente->celular,
                    'nome_fantasia' => $anuncio->cliente->nome_fantasia,
                    'nome_responsavel' => $anuncio->cliente->nome_responsavel,
                    'cpf_responsavel' => $anuncio->cliente->cpf_responsavel,

                    // 'cidade' => [
                    //     'id' => $anuncio->cliente->cidade->id,
                    //     'nome' => $anuncio->cliente->cidade->nome . " (" . $anuncio->cliente->cidade->estado->sigla . ")",
                    // ],
                ];
                if($anuncio->cliente->cidade){
                    $anuncioDTO['cliente']['cidade'] = [
                        'id' => $anuncio->cliente->cidade->id,
                        'nome' => $anuncio->cliente->cidade->nome . " (" . $anuncio->cliente->cidade->estado->sigla . ")",
                    ];
                    $anuncioDTO['cidadeAnunciante'] = $anuncio->cliente->cidade->nome . " (" . $anuncio->cliente->cidade->estado->sigla . ")";
                }
                if($inclurDownloads){
                    $anuncioDTO['cliente'] = [
                        ...$anuncioDTO['cliente'],
                        'imagem_cnh' => $anuncio->cliente->imagem_cnh,
                        'imagem_comprovante' => $anuncio->cliente->imagem_comprovante,
                        'imagem_doc_complementar' => $anuncio->cliente->imagem_doc_complementar,
                        'url_cnh' => route('admin.moderacao.download', ['id_cliente' => $anuncio->cliente->id, 'arquivo' => 'cnh']),
                        'url_comprovante' => route('admin.moderacao.download', ['id_cliente' => $anuncio->cliente->id, 'arquivo' => 'comp']),
                        'url_doc_complementar' => route('admin.moderacao.download', ['id_cliente' => $anuncio->cliente->id, 'arquivo' => 'doc']),
                        'type_cnh' => $anuncio->cliente->type_cnh,
                        'type_comprovante' => $anuncio->cliente->type_comprovante,
                        'type_doc_complementar' => $anuncio->cliente->type_doc_complementar,
                    ];
                }
            }
            // if($anuncio->modelo) {
            //     $anuncioDTO['modelo'] = [
            //         'id' => $anuncio->modelo->id,
            //         'descricao' => $anuncio->modelo->descricao,
            //         'marca' => [
            //             'id' => $anuncio->modelo->marca->id,
            //             'descricao' => $anuncio->modelo->marca->descricao,
            //         ],
            //     ];
            // }
            if($anuncio->usuarioModeracao) {
                $anuncioDTO['usuarioModeracao'] = [
                    'id' => $anuncio->usuarioModeracao->id,
                    'nome' => $anuncio->usuarioModeracao->nome,
                    'email' => $anuncio->usuarioModeracao->email,
                ];
            }
            if($anuncio->cor) {
                $anuncioDTO['cor'] = [
                    'id' => $anuncio->cor->id,
                    'descricao' => $anuncio->cor->descricao,
                ];
            }
            if($anuncio->tipoCambio) {
                $anuncioDTO['tipo_cambio'] = [
                    'id' => $anuncio->tipoCambio->id,
                    'descricao' => $anuncio->tipoCambio->descricao,
                ];
            }
            if($anuncio->tipoCombustivel) {
                $anuncioDTO['tipo_combustivel'] = [
                    'id' => $anuncio->tipoCombustivel->id,
                    'descricao' => $anuncio->tipoCombustivel->descricao,
                ];
            }
            if($anuncio->tipoPneu) {
                $anuncioDTO['tipo_pneu'] = [
                    'id' => $anuncio->tipoPneu->id,
                    'descricao' => $anuncio->tipoPneu->descricao,
                ];
            }
            if($anuncio->tipoParabrisa) {
                $anuncioDTO['tipo_parabrisa'] = [
                    'id' => $anuncio->tipoParabrisa->id,
                    'descricao' => $anuncio->tipoParabrisa->descricao,
                ];
            }
            if($anuncio->opcionais) {
                $opcionaisDTO = [];
                foreach ($anuncio->opcionais as $key => $opcional) {
                    $opDTO = [
                        'id' => $opcional->id,
                        'descricao' => $opcional->descricao,
                    ];
                    array_push($opcionaisDTO, $opDTO);
                }
                $anuncioDTO['opcionais'] = $opcionaisDTO;
            }
        }
        $basePath = ImagensAnuncio::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
        if($isRascunho){
            $basePath = ImagensRascunho::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $anuncio->id;
        }
        // Log::info(json_encode([
        //     'imagens' => $anuncio->imagens,
        //     'count imagens' => count($anuncio->imagens),
        // ]));
        if($incluirImagemPrincipal && count($anuncio->imagens) > 0) {
            $imagemPrincipal = $anuncio->imagens->firstWhere('principal', 1) ?? $anuncio->imagens->first();
            if($imagemPrincipal->link){
                $anuncioDTO['imagemPrincipal'] = $imagemPrincipal->link;
            } else {
                $arquivo = $imagemPrincipal->arquivo;
                if(Config::get('app.env') == 'production'){
                    $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $arquivo;
                } else {
                    $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $arquivo);
                }
                $anuncioDTO['imagemPrincipal'] = $urlImg;
            }
        }
        if($incluirTodasImagens && count($anuncio->imagens) > 0){
            $imagens = [];
            foreach ($anuncio->imagens as $key => $imagem) {
                if($imagem->link){
                    $urlImg = $imagem->link;
                } else {
                    if(Config::get('app.env') == 'production'){
                        $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $imagem->arquivo;
                    } else {
                        $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $imagem->arquivo);
                    }
                }
                array_push($imagens, $urlImg);
            }
            $anuncioDTO['imagens'] = $imagens;
        }
        // else {
        //     $anuncioDTO = [
        //         ...$anuncioDTO,
        //         'id_cliente' => $anuncio->id_cliente,
        //         'id_plano' => $anuncio->id_plano,
        //         'id_modelo' => $anuncio->id_modelo,
        //         'id_cor' => $anuncio->id_cor,
        //         'id_tipo_cambio' => $anuncio->id_tipo_cambio,
        //         'id_tipo_combustivel' => $anuncio->id_tipo_combustivel,
        //         'id_tipo_pneu' => $anuncio->id_tipo_pneu,
        //         'id_tipo_parabrisa' => $anuncio->id_tipo_parabrisa,
        //     ];
        // }
        return $anuncioDTO;
    }

    public static function getOpcionaisDTO($opcionais)
    {
        $opcionaisDTO = [];
        foreach ($opcionais as $key => $opcional) {
            $opDTO = [
                'id' => $opcional->id,
                'descricao' => $opcional->descricao,
            ];
            array_push($opcionaisDTO, $opDTO);
        }
        return $opcionaisDTO;
    }

    public static function getListaClientesDTO($clientes)
    {
        $clientesDTO = [];
        foreach ($clientes as $key => $cliente) {
            $clienteDTO = DtoHelper::getClienteDTO($cliente);
            array_push($clientesDTO, $clienteDTO);
        }
        return $clientesDTO;
    }

    public static function getClienteDTO($cliente)
    {
        $clienteDTO = [
            'id' => $cliente->id,
            'nome' => $cliente->usuario->nome,
            'email' => $cliente->usuario->email,
            'tipo' => $cliente->tipo,
            'num_documento' => $cliente->num_documento,
            'data_nasc' => $cliente->data_nasc ? DateTime::createFromFormat('Y-m-d', $cliente->data_nasc)->format('d/m/Y') : null,
            'telefone' => $cliente->telefone,
            'celular' => $cliente->celular,
            'cep' => $cliente->cep,
            'logradouro' => $cliente->logradouro,
            'numero' => $cliente->numero,
            'bairro' => $cliente->bairro,
            'complemento' => $cliente->complemento,
            'id_cidade' => $cliente->id_cidade,
            'imagem_cnh' => $cliente->imagem_cnh,
            'imagem_comprovante' => $cliente->imagem_comprovante,
            'imagem_doc_complementar' => $cliente->imagem_doc_complementar,
            'nome_fantasia' => $cliente->nome_fantasia,
            'nome_responsavel' => $cliente->nome_responsavel,
            'cpf_responsavel' => $cliente->cpf_responsavel,
            'inscricao_estadual' => $cliente->inscricao_estadual,
            'rg' => $cliente->rg,
            'id_usuario' => $cliente->id_usuario,
            'imagem_logo' => $cliente->imagem_logo,
            'imagem_capa' => $cliente->imagem_capa,
        ];

        if($cliente->cidade){
            $clienteDTO['cidade'] = [
                'id' => $cliente->cidade->id,
                'nome' => $cliente->cidade->nome . ' (' . $cliente->cidade->estado->sigla . ")",
            ];
        }

        if($cliente->usuario){
            $clienteDTO['usuario'] = DtoHelper::getUsuarioDTO($cliente->usuario);
        }

        if($cliente->anuncios){
            $clienteDTO['total_cliques'] = $cliente->anuncios->sum('num_cliques');
            $clienteDTO['total_anuncios'] = $cliente->anuncios->count();
        }

        if($cliente->dataUltimoAnuncio){
            $clienteDTO['data_ultimo_anuncio'] = $cliente->dataUltimoAnuncio;
        }

        $basePath = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $cliente->id;
        if($cliente->imagem_logo){
            if(Config::get('app.env') == 'production'){
                $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $cliente->imagem_logo;
            } else {
                $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $cliente->imagem_logo);
            }
            $clienteDTO['imagem_logo'] = $urlImg;
        }
        if($cliente->imagem_capa){
            if(Config::get('app.env') == 'production'){
                $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $cliente->imagem_capa;
            } else {
                $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $cliente->imagem_capa);
            }
            $clienteDTO['imagem_capa'] = $urlImg;
        }

        return $clienteDTO;
    }

    public static function getListaFaqDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'pergunta' => $item->pergunta,
                'resposta' => $item->resposta,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListaUfsDTO($itens, $exibeCidades = false)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'sigla' => $item->sigla,
                'nome' => $item->nome,
            ];
            if($exibeCidades){
                $itemDTO['cidades'] = DtoHelper::getListaCidadesDTO($item->cidades);
            }
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListaCidadesDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'nome' => $item->nome . " (".$item->estado->sigla.")",
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListagemCidadesDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'value' => $item->id,
                'label' => $item->nome . " (".$item->estado->sigla.")",
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListagemEstadosDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'value' => $item->id,
                'label' => $item->nome,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListagemAsyncSelectDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'value' => $item->id,
                'label' => $item->descricao,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListagemModelosDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'value' => $item->id ? $item->id : $key+1,
                'label' => $item->nome_curto,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getListaLicencasDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'tipo_plano' => $item->tipo_plano,
                'tipo_plano_str' => $item->tipo_plano_str,
                'num_licencas' => $item->num_licencas,
                'data_vencimento' => $item->data_vencimento,
            ];
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getLojaDTO($loja)
    {
        $lojaDTO = [
            'id' => $loja->id,
            'slug' => $loja->slug,
            'nome_fantasia' => $loja->nome_fantasia,
            'telefone' => $loja->telefone,
            'celular' => $loja->celular,
            'logradouro' => $loja->logradouro,
            'numero' => $loja->numero,
            'bairro' => $loja->bairro,
            'complemento' => $loja->complemento,
            'cep' => $loja->cep,
            'cidade' => $loja?->cidade?->nome,
            'estado' => $loja?->cidade?->estado->sigla,
        ];

        $basePath = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $loja->id;
        if($loja->imagem_logo){
            if(Config::get('app.env') == 'production'){
                $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $loja->imagem_logo;
            } else {
                $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $loja->imagem_logo);
            }
            $lojaDTO['imagem_logo'] = $urlImg;
        }
        if($loja->imagem_capa){
            if(Config::get('app.env') == 'production'){
                $urlImg = str_replace('https://', 'https://' . Config::get('filesystems.disks.spaces.bucket') . '.', Config::get('filesystems.disks.spaces.cdn_endpoint')) . DIRECTORY_SEPARATOR . $basePath . DIRECTORY_SEPARATOR . $loja->imagem_capa;
            } else {
                $urlImg = Base64Helper::convertImageToBase64String($basePath . DIRECTORY_SEPARATOR . $loja->imagem_capa);
            }
            $lojaDTO['imagem_capa'] = $urlImg;
        }

        return $lojaDTO;
    }
  
    public static function getLicencaAnuncioDTO($licenca)
    {
        if(!$licenca) return null;
        $licencaDTO = [
            'id' => $licenca->id,
            'tipo_plano' => $licenca->tipo_plano,
            'tipo_plano_str' => $licenca->tipo_plano_str,
            'num_licencas' => $licenca->num_licencas,
            'is_vencida' => $licenca->is_vencida,
            'data_vencimento' => Carbon::createFromFormat('Y-m-d', $licenca->data_vencimento)->format('d/m/Y'),
        ];
        return $licencaDTO;
    }

    public static function getListaLojasDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $item) {
            $listaItensDTO[] = self::getLojaDTO($item);
        }
        return $listaItensDTO;
    }

    public static function getListaPedidosDTO($itens, $exibePagamento = false)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = DtoHelper::getPedidoDTO($item, $exibePagamento);
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getPedidoDTO($item, $exibePagamento = false)
    {
        $itemDTO = [
            'id' => $item->id,
            'id_cliente' => $item->id_cliente,
            'id_anuncio' => $item->id_anuncio,
            'nome_plano' => $item->nome_plano,
            'tipo_plano' => $item->tipo_plano,
            'tipo_plano_str' => $item->tipo_plano_str,
            'quant_anuncios' => $item->quant_anuncios,
            'marca_modelo' => $item->marca_modelo,
            // 'versao_veiculo' => $item->versao_veiculo,
            'nome_proprietario' => $item->nome_proprietario,
            'localizacao_proprietario' => $item->localizacao_proprietario,
            'telefone_proprietario' => $item->telefone_proprietario,
            'celular_proprietario' => $item->celular_proprietario,
            'data_criacao' => $item->created_at->format('d/m/Y H:i'),
            'info_extra' => $item->info_extra,
            'tipo_pedido' => $item->tipo_plano && !$item->info_extra ? 'Compra de plano' :
                ($item->anuncio && $item->anuncio->id_cliente == $item->id_cliente && !$item->info_extra ? 'Criação de anúncio' : 
                ($item->info_extra && $item->info_extra == 'DESTAQUE' ? 'Compra de destaque' : 
                ($item->info_extra && $item->info_extra == 'RENOVACAO' ? 'Renovação de anúncio' : ''))),
        ];
        if($item->anuncio){
            $itemDTO['anuncio'] = DtoHelper::getAnuncioDTO($item->anuncio, false, true);
        }
        if($item->cliente){
            $itemDTO['cliente'] = [
                'id' => $item->cliente->id,
                'nome' => $item->cliente->usuario->nome,
                'email' => $item->cliente->usuario->email,
                'tipo' => $item->cliente->tipo,
                'nome_fantasia' => $item->cliente->nome_fantasia,
            ];

            if($item->cliente->cidade){
                $itemDTO['cliente']['cidade'] = [
                    'id' => $item->cliente->cidade->id,
                    'nome' => $item->cliente->cidade->nome . ' (' . $item->cliente->cidade->estado->sigla . ")",
                ];
            }
        }
        if($exibePagamento && $item->pagamento){
            $itemDTO['pagamento'] = DtoHelper::getPagamentoDTO($item->pagamento);
        }
        return $itemDTO;
    }

    public static function getPagamentoDTO($item)
    {
        $itemDTO = [
            'id' => $item->id,
            'id_status' => $item->id_status,
            'id_forma' => $item->id_forma,
            'valor' => $item->valor,
            'qr_code' => $item->qr_code,
            'qr_code_base64' => $item->qr_code_base64,
            'ticket_url' => $item->ticket_url,
            'is_expirado' => $item->is_expirado,
        ];
        if($item->formaPagamento){
            $itemDTO['forma_pagamento'] = [
                'id' => $item->formaPagamento->id,
                'codigo' => $item->formaPagamento->codigo,
                'descricao' => $item->formaPagamento->descricao,
            ];
        }
        if($item->statusPagamento){
            $itemDTO['status_pagamento'] = [
                'id' => $item->statusPagamento->id,
                'codigo' => $item->statusPagamento->codigo,
                'descricao' => $item->statusPagamento->descricao,
            ];
        }
        return $itemDTO;
    }

    public static function getInfoAnuncianteDTO($cliente)
    {
        $anuncianteDTO = [
            'id' => $cliente->id,
            'nome' => $cliente->nome_fantasia,
            'telefone' => $cliente->telefone,
            'celular' => $cliente->celular,
            'cidade' => [
                'id' => $cliente->cidade->id,
                'nome' => $cliente->cidade->nome . " (" . $cliente->cidade->estado->sigla . ")",
            ],
        ];
        return $anuncianteDTO;
    }

    public static function getListaComissoesDTO($itens)
    {
        $listaItensDTO = [];
        foreach ($itens as $key => $item) {
            $itemDTO = [
                'id' => $item->id,
                'percentual' => $item->percentual,
                'valor' => $item->valor,
                'percentual_str' => $item->percentual_str,
                'valor_str' => $item->valor_str,
                'data_comissao' => $item->created_at->format('d/m/Y'),
            ];
            if($item->pedido){
                $itemDTO['pedido'] = DtoHelper::getPedidoDTO($item->pedido, true);
            }
            if($item->anuncio){
                $itemDTO['anuncio'] = DtoHelper::getAnuncioDTO($item->anuncio);
            }
            array_push($listaItensDTO, $itemDTO);
        }
        return $listaItensDTO;
    }

    public static function getPayerMPDTO($cliente)
    {
        $firstName = substr($cliente->usuario->nome, 0, strpos($cliente->usuario->nome, " "));
        $lastName = substr($cliente->usuario->nome, strpos($cliente->usuario->nome, " "), strlen($cliente->usuario->nome));
        $clienteDTO = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $cliente->usuario->email,
            'identification' => [
                'type' => $cliente->tipo == 'PJ' ? 'CNPJ' : 'CPF',
                'number' => $cliente->num_documento,
            ],
            'address' => [
                'zipCode' => $cliente->cep,
                'federalUnit' => $cliente->cidade->estado->nome,
                'city' => $cliente->cidade->nome,
                'neighborhood' => $cliente->bairro,
                'streetName' => $cliente->logradouro,
                'streetNumber' => $cliente->numero,
                'complement' => $cliente->complemento,
            ]
        ];
        
        return $clienteDTO;
    }

    public static function getParametrosGeraisDTO($itens)
    {
        $itensDTO = [];
        foreach ($itens as $key => $item) {
            $itensDTO[$item->chave] = $item->valor;
        }
        return $itensDTO;
    }
}

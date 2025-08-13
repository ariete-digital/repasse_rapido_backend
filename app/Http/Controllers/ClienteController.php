<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Helpers\IntegratorHelper;
use App\Jobs\ProcessarIntegracao;
use App\Models\Anuncio;
use App\Models\Cliente;
use App\Models\IntegracaoLojaConectada;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        $paginacao = Cliente::with('usuario', 'anuncios');
        if ($request->filtro) {
            $paginacao = $paginacao->where(function ($query) use ($request) {
                    $query->whereRelation('usuario', 'nome', 'LIKE', '%' . $request->filtro . '%')
                        ->orWhereRelation('usuario', 'email', 'LIKE', '%' . $request->filtro . '%')
                        ->orWhereRelation('usuario', 'nome_fantasia', 'LIKE', '%' . $request->filtro . '%');
                });
        }
        if($request->tipoPessoa && $request->tipoPessoa != "TODOS"){
            $paginacao = $paginacao->where("tipo", $request->tipoPessoa);
        }
        $paginacao = $paginacao->paginate(ClienteController::NUM_REG_POR_PAG);

        $listaClientesDTO = DtoHelper::getListaClientesDTO($paginacao->items());

        return $this->getResponse('success', [
            'listaClientes' => $listaClientesDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
                'totalRegistros' => $paginacao->total(),
            ]
        ]);
    }

    public function obter(Request $request)
    {
        $clienteDTO = [];
        if ($request->id) {
            $cliente = Cliente::where('id', $request->id)->with('usuario')->first();
            $clienteDTO = DtoHelper::getClienteDTO($cliente);
        }
        return $this->getResponse('success', [
            'cliente' => $clienteDTO,
        ]);
    }
    
    public function obterDetalhes(Request $request)
    {
        $clienteDTO = [];
        if($request->id){
            $cliente = Cliente::where('id', $request->id)->with('usuario')->first();
            $clienteDTO = DtoHelper::getClienteDTO($cliente);
        }

        // $anuncios = Anuncio::where('id_cliente', $cliente->id);
        // if($request->filtro){
        //     $anuncios = $anuncios->where('codigo', 'LIKE', '%'.$request->filtro.'%')
        //         ->orWhereRelation('cliente', 'nome', 'LIKE', '%'.$request->filtro.'%');
        // }
        // $anuncios = $anuncios->get();
        // $anunciosDTO = DtoHelper::getListaAnunciosDTO($anuncios, false, false);

        $dataPrimeiroAnuncio = DateTime::createFromFormat('Y-m-d H:i:s', Anuncio::where('id_cliente', $cliente->id)->min('created_at'))->format('d/m/Y');
        $dataUltimoAnuncio = DateTime::createFromFormat('Y-m-d H:i:s', Anuncio::where('id_cliente', $cliente->id)->max('created_at'))->format('d/m/Y');
        $contadorTotal = Anuncio::where('id_cliente', $cliente->id)->count();
        $contadorAtivos = Anuncio::where('id_cliente', $cliente->id)->where('ativo', 1)->count();
        $contadorEncerrados = Anuncio::where('id_cliente', $cliente->id)->where('ativo', 0)->count();
        $primeiroAnuncio = Anuncio::where('id_cliente', $cliente->id)->orderBy('created_at')->with('usuarioModeracao')->first();
        $ultimoAnuncio = Anuncio::where('id_cliente', $cliente->id)->orderBy('created_at', 'desc')->with('usuarioModeracao')->first();
        $usuarioPrimeiraModeracao = null;
        if($primeiroAnuncio){
            if($primeiroAnuncio->usuarioModeracao){
                $usuarioPrimeiraModeracao = $primeiroAnuncio->usuarioModeracao->nome . " - " . $primeiroAnuncio->usuarioModeracao->email;
            } else {
                $usuarioPrimeiraModeracao = 'Anúncio não moderado';
            }
        }
        $usuarioUltimaModeracao = null;
        if($ultimoAnuncio){
            if($ultimoAnuncio->usuarioModeracao){
                $usuarioUltimaModeracao = $ultimoAnuncio->usuarioModeracao->nome . " - " . $ultimoAnuncio->usuarioModeracao->email;
            } else {
                $usuarioUltimaModeracao = 'Anúncio não moderado';
            }
        }

        return $this->getResponse('success', [
            'cliente' => $clienteDTO,
            // 'listaAnuncios' => $anunciosDTO,
            'dataPrimeiroAnuncio' => $dataPrimeiroAnuncio,
            'dataUltimoAnuncio' => $dataUltimoAnuncio,
            'totalAnuncios' => $contadorTotal,
            'anunciosAtivos' => $contadorAtivos,
            'anunciosEncerrados' => $contadorEncerrados,
            'usuarioPrimeiraModeracao' => $usuarioPrimeiraModeracao,
            'usuarioUltimaModeracao' => $usuarioUltimaModeracao,
        ]);
    }

    public function salvar(Request $request)
    {
        // Log::info(json_encode([
        //     'request' => $request->all()
        // ]));
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        $cliente = Cliente::where('id', $request->id)->with('usuario')->first();

        $cliente->usuario->nome = $request->nome;
        $cliente->usuario->email = $request->email;
        if ($request->senha) {
            $cliente->usuario->password = Hash::make($request->senha);
        }
        $cliente->usuario->active = $request->active === "true";
        $cliente->usuario->save();

        if($request->telefone){
            $cliente->telefone = $request->telefone;
        }
        if($request->celular){
            $cliente->celular = $request->celular;
        }
        if($request->num_documento){
            $cliente->num_documento = $request->num_documento;
        }
        if($request->data_nasc){
            $cliente->data_nasc = DateTime::createFromFormat('d/m/Y', $request->data_nasc);
        }
        if($request->cep){
            $cliente->cep = $request->cep;
        }
        if($request->logradouro){
            $cliente->logradouro = $request->logradouro;
        }
        if($request->numero){
            $cliente->numero = $request->numero;
        }
        if($request->complemento){
            $cliente->complemento = $request->complemento;
        }
        if($request->bairro){
            $cliente->bairro = $request->bairro;
        }
        if($request->id_cidade){
            $cliente->id_cidade = $request->id_cidade;
        }
        if($request->nome_fantasia){
            $cliente->nome_fantasia = $request->nome_fantasia;
        }
        if($request->cpf_responsavel){
            $cliente->cpf_responsavel = $request->cpf_responsavel;
        }
        if($request->nome_responsavel){
            $cliente->nome_responsavel = $request->nome_responsavel;
        }
        if($request->inscricao_estadual){
            $cliente->inscricao_estadual = $request->inscricao_estadual;
        }
        if($request->rg){
            $cliente->rg = $request->rg;
        }

        $basePath = Cliente::BASE_UPLOAD_PATH . DIRECTORY_SEPARATOR . $cliente->id;
        if($request->comprovEnd){
            $file = $request->comprovEnd;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_comprovante = $file->getClientOriginalName();
        }
        if($request->cnh){
            $file = $request->cnh;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_cnh = $file->getClientOriginalName();
        }
        if($request->docComplementar){
            $file = $request->docComplementar;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_doc_complementar = $file->getClientOriginalName();
        }
        if($request->capa){
            $file = $request->capa;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_capa = $file->getClientOriginalName();
        }
        if($request->logo){
            $file = $request->logo;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_logo = $file->getClientOriginalName();
        }

        $cliente->save();

        return $this->getResponse('success', [
            'message' => "Cliente salvo com sucesso!"
        ]);
    }

    public function excluir(Request $request)
    {
        $cliente = Cliente::where('id', $request->id)->first();
        $cliente->delete();
        return $this->getResponse('success', [
            'message' => "Cliente excluído com sucesso!"
        ]);
    }

    public function executarIntegracao(Request $request)
    {
        $integracao = IntegracaoLojaConectada::create(['status' => 'pendente']);

        ProcessarIntegracao::dispatch($integracao);

        return $this->getResponse('success', [
            'message' => "Processo de integração iniciado, o status será atualizado automaticamente!",
            'id' => $integracao->id
        ]);
    }

    public function obterIdIntegracao(Request $request)
    {
        $integracao = IntegracaoLojaConectada::where('status', 'pendente')->orWhere('status', 'em_progresso')->first();

        $id = null;
        if($integracao) $id = $integracao->id;
        Log::info($integracao);

        return $this->getResponse('success', [
            'id' => $id,
        ]);
    }

    public function verificarStatus(Request $request)
    {
        $integracao = IntegracaoLojaConectada::findOrFail($request->id);

        return $this->getResponse('success', [
            'status' => $integracao->status,
            'resultado' => $integracao->resultado,
        ]);
    }
}

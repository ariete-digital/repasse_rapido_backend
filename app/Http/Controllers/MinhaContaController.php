<?php

namespace App\Http\Controllers;

use App\Helpers\ClienteHelper;
use App\Helpers\DtoHelper;
use App\Models\Cidade;
use App\Models\Cliente;
use App\Models\Uf;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MinhaContaController extends Controller
{
    public function obter(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario', 'cidade.estado')->first();

        return $this->getResponse('success', [
            'cliente' => DtoHelper::getClienteDTO($cliente),
        ]);
    }

    public function adminObter(Request $request)
    {
        return $this->getResponse('success', [
            'usuario' => DtoHelper::getUsuarioDTO(Auth::user()),
        ]);
    }

    public function salvar(Request $request)
    {
        $cliente = Cliente::where('id_usuario', Auth::id())->with('usuario')->first();
        if($request->nova_senha){
            $cliente->usuario->password = Hash::make($request->nova_senha);
            $cliente->usuario->save();
        }
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
        if($request->imgLogo){
            $file = $request->imgLogo;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_logo = $file->getClientOriginalName();
        }
        if($request->imgCapa){
            $file = $request->imgCapa;
            Storage::makeDirectory($basePath, 0775, true);
            $filePath = Storage::putFileAs(
                $basePath,
                $file,
                $file->getClientOriginalName()
            );
            $cliente->imagem_capa = $file->getClientOriginalName();
        }

        $cliente->save();

        $isMinhaContaCompleta = ClienteHelper::isMinhaContaCompleta();

        return $this->getResponse('success', [
            'message' => "Informações salvas com sucesso!",
            'isMinhaContaCompleta' => $isMinhaContaCompleta
        ]);
    }

    public function listagemCidades(Request $request){
        $cidades = Cidade::where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->with('estado')
            ->limit(100)
            ->get();
        // Log::info($cidades);
        $cidadesDTO = DtoHelper::getListagemCidadesDTO($cidades);
        return $this->getResponse('success', $cidadesDTO);
    }

    public function listagemEstados(Request $request){
        $estados = Uf::where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->limit(30)
            ->get();
        // Log::info($cidades);
        $estadosDTO = DtoHelper::getListagemEstadosDTO($estados);
        return $this->getResponse('success', $estadosDTO);
    }

    public function obterCidade(Request $request){
        $cidades = Cidade::where('nome', $request->nome)
            ->with('estado')
            ->limit(100)
            ->get();
        // Log::info($cidades);
        $cidadesDTO = DtoHelper::getListagemCidadesDTO($cidades);
        return $this->getResponse('success', $cidadesDTO);
    }

    public function listagemTodasCidades(Request $request){
        // Log::info('chamou listagemTodasCidades');
        $cidades = Cidade::where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->with('estado')
            ->orderBy('nome')
            ->get();
        // Log::info($cidades);
        $cidadesDTO = DtoHelper::getListagemCidadesDTO($cidades);
        return $this->getResponse('success', $cidadesDTO);
    }

    public function listagemCidadesCadastradas(Request $request){
        // Log::info('chamou listagemCidadesCadastradas');
        $cidades = Cidade::has('clientes')
            ->where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->with('estado')
            ->orderBy('nome')
            ->get();
        // Log::info($cidades);
        $cidadesDTO = DtoHelper::getListagemCidadesDTO($cidades);
        return $this->getResponse('success', $cidadesDTO);
    }

    public function listagemEstadosCadastradas(Request $request){
        // Log::info('chamou listagemCidadesCadastradas');
        $estados = Uf::has('cidades.clientes')
            ->where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->orderBy('nome')
            ->get();
        // Log::info($cidades);
        $estadosDTO = DtoHelper::getListagemEstadosDTO($estados);
        return $this->getResponse('success', $estadosDTO);
    }

    public function listagemCidadesBanners(Request $request){
        // Log::info('chamou listagemCidadesCadastradas');
        $cidades = Cidade::has('banners')
            ->where('nome', 'LIKE', '%'.$request->filtro.'%')
            ->with('estado')
            ->orderBy('nome')
            ->get();
        // Log::info($cidades);
        $cidadesDTO = DtoHelper::getListagemCidadesDTO($cidades);
        return $this->getResponse('success', $cidadesDTO);
    }
}

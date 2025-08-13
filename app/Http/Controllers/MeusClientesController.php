<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Anuncio;
use App\Models\Cliente;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeusClientesController extends Controller
{
    const NUM_REG_POR_PAG = 10;

    public function lista(Request $request)
    {
        $paginacao = Cliente::whereRelation('pedidos.comissoes', 'id_usuario', Auth::id())->with('usuario', 'anuncios')->has('pedidos.comissoes');
        if ($request->filtro) {
            $paginacao = $paginacao->where(function ($query) use ($request) {
                    $query->whereRelation('usuario', 'nome', 'LIKE', '%' . $request->filtro . '%')
                        ->orWhereRelation('usuario', 'email', 'LIKE', '%' . $request->filtro . '%');
                });
        }
        if($request->tipoPessoa && $request->tipoPessoa != "TODOS"){
            $paginacao = $paginacao->where("tipo", $request->tipoPessoa);
        }
        $sql = $paginacao->toSql();
        $paginacao = $paginacao->paginate(ClienteController::NUM_REG_POR_PAG);

        $listaClientesDTO = DtoHelper::getListaClientesDTO($paginacao->items());

        return $this->getResponse('success', [
            'sql' => $sql,
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
        $anuncios = [];
        if ($request->id) {
            $cliente = Cliente::where('id', $request->id)->with('cidade.estado')->first();
            $clienteDTO = DtoHelper::getClienteDTO($cliente);
            if ($request->filtro) {
                $paginacao = Anuncio::where('id_cliente', $cliente->id)
                    ->where('codigo', 'LIKE', '%' . $request->filtro . '%')
                    ->paginate(MeusClientesController::NUM_REG_POR_PAG);
            } else {
                $paginacao = Anuncio::where('id_cliente', $cliente->id)->paginate(MeusClientesController::NUM_REG_POR_PAG);
            }
            $anuncios = $paginacao->items();
            $anunciosDTO = DtoHelper::getListaAnunciosDTO($anuncios);

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
        }
        return $this->getResponse('success', [
            'cliente' => $clienteDTO,
            'anuncios' => $anunciosDTO,
            'dataPrimeiroAnuncio' => $dataPrimeiroAnuncio,
            'dataUltimoAnuncio' => $dataUltimoAnuncio,
            'totalAnuncios' => $contadorTotal,
            'anunciosAtivos' => $contadorAtivos,
            'anunciosEncerrados' => $contadorEncerrados,
            'usuarioPrimeiraModeracao' => $usuarioPrimeiraModeracao,
            'usuarioUltimaModeracao' => $usuarioUltimaModeracao,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }
}

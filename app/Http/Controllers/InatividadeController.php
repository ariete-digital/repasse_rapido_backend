<?php

namespace App\Http\Controllers;

use App\Helpers\DtoHelper;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InatividadeController extends Controller
{
    public function obterListaUsuarios(Request $request)
    {
        $paginacao = User::where('role', 'vendedor')->with('subregiao')->paginate();
        $usuarios = $paginacao->items();
        $usuariosDTO = DtoHelper::getListaUsuariosDTO($usuarios, true);

        return $this->getResponse('success', [
            'usuarios' => $usuariosDTO,
            'paginacao' => [
                'paginaAtual' => $paginacao->currentPage(),
                'ultimaPagina' => $paginacao->lastPage(),
            ]
        ]);
    }
    
    public function obterInfo(Request $request)
    {
        // $user = Auth::user();
        // if($user->role == 'admin' || $user->role == 'superadmin'){
        //     $gerentes = User::where('role', 'gerente')->with('escritorio.subregioes.');
        // }
        $idUsuario = Auth::id();
        if($request->id_usuario){
            $idUsuario = $request->id_usuario;
        }

        $usuario = User::where('id', $idUsuario)->with('subregiao')->first();
        
        $idsUfs = $usuario->subregiao->ufs->pluck('id');
        $clientes = Cliente::with('usuario', 'anuncios')->whereHas('cidade', function (Builder $query) use ($idsUfs){
                $query->whereIn('id_uf', $idsUfs);
            });
        if($request->filtro){
            $clientes = $clientes->where('nome', 'LIKE', '%'.$request->filtro.'%');
        }
        $clientes = $clientes->get();

        foreach ($usuario->subregiao->faixasCep as $key => $faixa) {
            $cepInicial = intval($faixa['cep_inicial']);
            $cepFinal = intval($faixa['cep_final']);
            $listaClientes = Cliente::where(DB::raw("CONVERT(REPLACE(REPLACE(cep, '-', ''), '.', ''), UNSIGNED)"), '>=' , $cepInicial)
                ->where(DB::raw("CONVERT(REPLACE(REPLACE(cep, '-', ''), '.', ''), UNSIGNED)"), '<' , $cepFinal)
                ->with('usuario', 'anuncios');
            if($request->filtro){
                $listaClientes = $listaClientes->where('nome', 'LIKE', '%'.$request->filtro.'%');
            }
            $listaClientes = $listaClientes->get();
            $clientes = $clientes->concat($listaClientes);
        }

        $listaInativos = collect([]);
        foreach ($clientes as $key => $cliente) {
            if(count($cliente->anuncios)){
                $dataUltimoAnuncio = $cliente->anuncios->max('created_at');
                $dataUltimoAnuncio->setTime(0,0,0);
                $dataLimite = Carbon::now()->setTime(0,0,0)->addDays(-30);
                if($dataUltimoAnuncio->lt($dataLimite)){
                    $cliente->dataUltimoAnuncio = $dataUltimoAnuncio->format('d/m/Y');
                    $listaInativos = $listaInativos->push($cliente);
                }
            } else {
                $listaInativos = $listaInativos->push($cliente);
            }
        }

        $percentualInativos = count($listaInativos) * 100 / count($clientes);

        $listaInativosDTO = DtoHelper::getListaClientesDTO($listaInativos);

        return $this->getResponse('success', [
            'listaInativos' => $listaInativosDTO,
            'percentualInativos' => $percentualInativos,
        ]);
    }
}

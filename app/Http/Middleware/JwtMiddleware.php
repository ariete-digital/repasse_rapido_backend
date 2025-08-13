<?php

namespace App\Http\Middleware;

use App\Helpers\AuthHelper;
use Closure;
use Exception;
use Illuminate\Support\Facades\Log;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            //verificar se a role do usuario pode acessar a rota
            if(!AuthHelper::podeExecutarRota($request->path(), $user->role)){
                return response()->json([
                    'status'   => "unauthorized",
                ], 401);
            }
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json([
                    'status'   => "invalid",
                ], 401);
            } else if ($e instanceof TokenExpiredException) {
                if($request->path() === 'api/refresh'){
                    return $next($request);
                }
                return response()->json([
                    'status'   => "expired",
                ]);
            } else {
                Log::error($e);
                return response()->json([
                    'status'   => "invalid",
                ], 404);
            }
        }
        return $next($request);
    }
}

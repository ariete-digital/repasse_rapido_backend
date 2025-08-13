<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        $possuiAcesso = false;
        foreach ($roles as $key => $role) {
            if ($user && $user->role == $role) $possuiAcesso = true;
        }
        if (!$possuiAcesso) {
            return response()->json([
                'status'   => "unauthorized",
                'message' => 'Acesso não autorizado'
            ], 401);
        }

        return $next($request);
    }
}

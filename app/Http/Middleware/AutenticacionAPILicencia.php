<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutenticacionAPILicencia
{

    public function handle(Request $request, Closure $next)
    {
        $usuario = $request->headers->get('usuario');
        $clave = $request->headers->get('clave');

        if ($usuario == 'Perseo' && $clave == 'Perseo1232*') {
            return $next($request);
        } else {
            return response()->json(['error' => 'No autorizado'], 401);
        }
    }
}

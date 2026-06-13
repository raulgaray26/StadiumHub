<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EsComite
 *
 * Protege las rutas del Comité FIFA.
 * Solo permite el acceso si el usuario autenticado tiene rol_id = 1.
 * Cualquier otro rol recibirá un error 403 Forbidden.
 */
class EsComite
{
    /**
     * Maneja el request entrante.
     *
     * @param Request  $request   El request HTTP actual.
     * @param Closure  $next      El siguiente middleware/handler en la cadena.
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado Y que su rol sea Comité (1)
        if (!auth()->check() || (int) auth()->user()->rol_id !== 1) {
            // Retornar error 403 con mensaje en español
            abort(403, 'Acceso restringido: Se requiere rol de Comité FIFA.');
        }

        // Si el rol es correcto, continuar con el request
        return $next($request);
    }
}
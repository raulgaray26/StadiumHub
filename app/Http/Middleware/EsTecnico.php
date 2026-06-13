<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EsTecnico
 *
 * Protege las rutas del Técnico de Campo.
 * Solo permite el acceso si el usuario autenticado tiene rol_id = 3.
 */
class EsTecnico
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado Y que su rol sea Técnico (3)
        if (!auth()->check() || (int) auth()->user()->rol_id !== 3) {
            abort(403, 'Acceso restringido: Se requiere rol de Técnico de Campo.');
        }

        return $next($request);
    }
}
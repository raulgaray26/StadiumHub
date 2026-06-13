<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware EsJefe
 *
 * Protege las rutas del Jefe de Mantenimiento.
 * Solo permite el acceso si el usuario autenticado tiene rol_id = 2.
 */
class EsJefe
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado Y que su rol sea Jefe (2)
        if (!auth()->check() || (int) auth()->user()->rol_id !== 2) {
            abort(403, 'Acceso restringido: Se requiere rol de Jefe de Mantenimiento.');
        }

        return $next($request);
    }
}
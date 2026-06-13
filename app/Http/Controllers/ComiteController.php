<?php

namespace App\Http\Controllers;

use App\Models\Estadio;
use App\Models\HistorialTarea;
use App\Models\Tarea;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ComiteController — Lógica del panel de auditoría del Comité FIFA.
 *
 * El Comité tiene visibilidad GLOBAL sobre todos los estadios y acciones.
 * Su dashboard es un centro de control con:
 *   - Estadísticas agregadas del sistema completo.
 *   - Historial completo de auditoría (historial_tareas).
 *   - Estado de todos los estadios sede.
 *
 * Acceso: Solo usuarios con rol_id = 1 (middleware 'rol.comite' aplicado en rutas).
 */
class ComiteController extends Controller
{
    /**
     * Dashboard de auditoría global del Comité FIFA.
     *
     * Usa paginación en el historial para evitar cargar miles de registros.
     * El eager loading ('with') previene el problema N+1 de consultas.
     *
     * @return View
     */
    public function dashboard(): View
    {
        // ─── Estadísticas globales del sistema ────────────────────────────
        $totalTareas       = Tarea::count();
        $tareasCompletadas = Tarea::where('estado', 'Completada')->count();
        $tareasPendientes  = Tarea::where('estado', 'Pendiente')->count();
        $totalUsuarios     = Usuario::count();
        $totalEstadios     = Estadio::count();

        // ─── Historial de auditoría paginado ──────────────────────────────
        // Con eager loading de relaciones para evitar N+1 queries
        $historial = HistorialTarea::with([
                'tarea',          // Datos de la tarea
                'usuario',        // Usuario que ejecutó la acción
                'creador',        // Usuario que originó la acción (ej: jefe que asignó)
            ])
            ->orderBy('timestamp', 'desc') // Más recientes primero
            ->paginate(25);                // 25 registros por página

        // ─── Estado de cada estadio sede ──────────────────────────────────
        // withCount() añade {relation}_count sin cargar todos los registros
        $estadios = Estadio::withCount(['tareas', 'usuarios'])
            ->get()
            ->map(function ($estadio) {
                // Calcular tareas completadas por estadio para la barra de progreso
                $estadio->tareas_completadas = Tarea::where('estadio_id', $estadio->estadio_id)
                    ->where('estado', 'Completada')
                    ->count();
                return $estadio;
            });

        return view('comite.dashboard', compact(
            'totalTareas',
            'tareasCompletadas',
            'tareasPendientes',
            'totalUsuarios',
            'totalEstadios',
            'historial',
            'estadios'
        ));
    }
}
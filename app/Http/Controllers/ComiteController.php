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
     * Dashboard global con filtros por estadio y tipo de acción.
     * Recibe parámetros GET: ?estadio_id=X&accion=Y
     */
    public function dashboard(Request $request): View
    {
        // ─── Estadísticas globales ─────────────────────────────────────
        $totalTareas       = Tarea::count();
        $tareasCompletadas = Tarea::where('estado', 'Completada')->count();
        $tareasPendientes  = Tarea::where('estado', 'Pendiente')->count();
        $totalUsuarios     = Usuario::count();
        $totalEstadios     = Estadio::count();

        // ─── Filtros (vienen de GET params del formulario) ─────────────
        $filtroEstadio = $request->input('estadio_id');   // null si no se filtra
        $filtroAccion  = $request->input('accion');        // null si no se filtra

        // ─── Historial con filtros opcionales y eager loading ──────────
        $query = HistorialTarea::with(['tarea', 'usuario', 'creador'])
            ->orderBy('timestamp', 'desc');

        if ($filtroEstadio) {
            // Filtrar por estadio: join con tareas para acceder a estadio_id
            $query->whereHas('tarea', function ($q) use ($filtroEstadio) {
                $q->where('estadio_id', $filtroEstadio);
            });
        }

        if ($filtroAccion) {
            $query->where('accion', $filtroAccion);
        }

        $historial = $query->paginate(15)->withQueryString();
        // withQueryString() preserva los filtros al paginar

        // ─── Estado de estadios con contadores ─────────────────────────
        $estadios = Estadio::withCount(['tareas', 'usuarios'])->get()
            ->map(function ($estadio) {
                $estadio->tareas_completadas = Tarea::where('estadio_id', $estadio->estadio_id)
                    ->where('estado', 'Completada')->count();
                return $estadio;
            });

        // ─── Opciones para los dropdowns de filtro ─────────────────────
        $todosEstadios = Estadio::orderBy('nombre')->get();
        $acciones      = ['Creada', 'Asignada', 'Completada', 'Editada'];

        return view('comite.dashboard', compact(
            'totalTareas', 'tareasCompletadas', 'tareasPendientes',
            'totalUsuarios', 'totalEstadios',
            'historial', 'estadios',
            'todosEstadios', 'acciones',
            'filtroEstadio', 'filtroAccion'
        ));
    }

    /**
     * Vista de todas las tareas de un estadio específico.
     * Ruta: GET /comite/estadio/{id}
     */
    public function verEstadio(int $id): View
    {
        $estadio = Estadio::findOrFail($id);

        $tareas = Tarea::where('estadio_id', $id)
            ->with(['tipoTarea', 'creador', 'usuariosAsignados'])
            ->orderBy('fecha_limite', 'asc')
            ->get();

        return view('comite.estadio', compact('estadio', 'tareas'));
    }
}
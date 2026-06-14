<?php

namespace App\Http\Controllers;

use App\Models\HistorialTarea;
use App\Models\Tarea;
use App\Models\TareaUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * TecnicoController — Lógica del panel del Técnico de Campo.
 *
 * Funcionalidades:
 *   1. dashboard(): Muestra las tareas asignadas al técnico autenticado.
 *   2. completarTarea(): Marca una asignación como 'Completada' y registra en historial.
 *
 * Acceso: Solo usuarios con rol_id = 3 (middleware 'rol.tecnico' aplicado en rutas).
 */
class TecnicoController extends Controller
{
    /**
     * Muestra el dashboard del técnico con sus tareas asignadas.
     *
     * Consulta la relación many-to-many 'tareasAsignadas' del modelo Usuario,
     * que une 'usuarios' con 'tareas' a través de 'tarea_user'.
     * Con eager loading (with()) se cargan las relaciones anidadas eficientemente.
     *
     * @return View
     */
    public function dashboard(): View
    {
        // Obtener el usuario autenticado (técnico)
        $usuario = Auth::user();

        // Cargar las tareas asignadas con sus relaciones necesarias para la vista.
        // orderBy 'fecha_limite' muestra primero las más urgentes.
        $tareas = $usuario->tareasAsignadas()
            ->with(['tipoTarea', 'estadio', 'creador'])
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Contadores para las tarjetas de estadísticas del dashboard
        $totalTareas      = $tareas->count();
        $tareasPendientes = $tareas->filter(fn($t) => $t->pivot->estado_asignacion === 'Pendiente')->count();
        $tareasCompletadas = $tareas->filter(fn($t) => $t->pivot->estado_asignacion === 'Completada')->count();

        return view('tecnico.dashboard', compact(
            'usuario',
            'tareas',
            'totalTareas',
            'tareasPendientes',
            'tareasCompletadas'
        ));
    }

    /**
     * Marca una tarea como completada para el técnico autenticado.
     *
     * Flujo:
     * 1. Encuentra el registro en 'tarea_user' para este técnico y tarea.
     * 2. Actualiza el estado y la fecha de completado.
     * 3. Crea un registro en 'historial_tareas' para auditoría.
     * 4. Redirige al dashboard con mensaje de éxito.
     *
     * @param  Request  $request  Puede incluir 'observaciones' opcionales.
     * @param  int      $id       ID de la tarea a completar (tarea_id).
     * @return RedirectResponse
     */
    public function completarTarea(Request $request, int $id): RedirectResponse
    {
        $tecnico = Auth::user();

        // 1. Validar observaciones opcionales
        $validado = $request->validate([
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);

        // 2. Buscar el registro de asignación en la tabla pivote 'tarea_user'
        //    para ESTE técnico y ESTA tarea específica.
        $asignacion = TareaUser::where('tarea_id', $id)
            ->where('user_id', $tecnico->user_id)
            ->firstOrFail(); // 404 si no está asignado

        // 3. Guardar el estado anterior para el historial de auditoría
        $estadoAnterior = $asignacion->estado_asignacion;

        // 4. Evitar marcar como completada una tarea ya completada
        if ($estadoAnterior === 'Completada') {
            return redirect()->route('tecnico.dashboard')
                ->with('error', 'Esta tarea ya está marcada como completada.');
        }

        // 5. Actualizar la asignación pivote: cambiar estado y registrar fecha
        $asignacion->update([
            'estado_asignacion' => 'Completada',
            'fecha_completado'  => now()->toDateString(),
            'observaciones'     => $validado['observaciones'] ?? null,
        ]);

        // 6. Obtener la tarea principal
        $tarea = Tarea::findOrFail($id);
        
        // 6.1 SINCRONIZACIÓN CLAVE: Actualizar el estado general de la tarea 
        // para que el Comité y el Jefe la vean como completada
        $tarea->update([
            'estado' => 'Completada'
        ]);

        // 7. Registrar la acción en el historial de auditoría
        //    Esto permite al Comité FIFA ver quién completó qué y cuándo.
        HistorialTarea::create([
            'tarea_id'       => $id,
            'user_id'        => $tecnico->user_id,
            'creador_id'     => $tecnico->user_id, // El técnico es el actor en este caso
            'accion'         => 'Completada',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo'   => 'Completada',
            'detalle'        => 'Tarea completada por el técnico ' . $tecnico->nombre
                                . ($validado['observaciones'] ? '. Obs: ' . $validado['observaciones'] : ''),
            'timestamp'      => now(),
        ]);

        return redirect()->route('tecnico.dashboard')
            ->with('success', '¡Tarea "' . $tarea->titulo . '" marcada como completada y sincronizada!');
    }
}
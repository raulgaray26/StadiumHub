<?php

namespace App\Http\Controllers;

use App\Models\HistorialTarea;
use App\Models\Tarea;
use App\Models\TareaUser;
use App\Models\TipoTarea;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * JefeController — Lógica del panel del Jefe de Mantenimiento.
 *
 * Funcionalidades:
 *   1. dashboard(): Vista general con estadísticas y listado de tareas del estadio.
 *   2. crearTareaForm(): Formulario para crear una nueva tarea.
 *   3. storeTarea(): Persiste la nueva tarea en BD y crea registro en historial.
 *   4. asignarForm(): Formulario para asignar una tarea a uno o más técnicos.
 *   5. asignarTarea(): Crea registros en 'tarea_user' y actualiza historial.
 *
 * Acceso: Solo usuarios con rol_id = 2 (middleware 'rol.jefe' aplicado en rutas).
 * Restricción: El jefe solo puede ver y gestionar el estadio al que está asignado.
 */
class JefeController extends Controller
{
    /**
     * Dashboard principal del Jefe de Mantenimiento.
     *
     * Muestra estadísticas generales y la lista de tareas del estadio
     * junto con los técnicos disponibles para asignar trabajo.
     *
     * @return View
     */
    public function dashboard(): View
    {
        $jefe = Auth::user();

        // Cargar todas las tareas del estadio del jefe con sus relaciones
        $tareas = Tarea::where('estadio_id', $jefe->estadio_id)
            ->with(['tipoTarea', 'creador', 'usuariosAsignados'])
            ->orderBy('fecha_limite', 'asc')
            ->get();

        // Cargar técnicos del mismo estadio para mostrar en el dashboard
        $tecnicos = Usuario::where('estadio_id', $jefe->estadio_id)
            ->where('rol_id', 3) // Solo técnicos de campo
            ->get();

        // Calcular estadísticas para las tarjetas del dashboard
        $totalTareas       = $tareas->count();
        $tareasCompletadas = $tareas->where('estado', 'Completada')->count();
        $tareasPendientes  = $tareas->where('estado', 'Pendiente')->count();
        $tareasEnProgreso  = $tareas->where('estado', 'En Progreso')->count();

        return view('jefe.dashboard', compact(
            'jefe', 'tareas', 'tecnicos',
            'totalTareas', 'tareasCompletadas', 'tareasPendientes', 'tareasEnProgreso'
        ));
    }

    /**
     * Muestra el formulario para crear una nueva tarea de mantenimiento.
     *
     * @return View
     */
    public function crearTareaForm(): View
    {
        // Cargar todos los tipos de tarea disponibles para el dropdown
        $tiposTarea = TipoTarea::orderBy('nombre')->get();
        $jefe       = Auth::user();

        return view('jefe.crear-tarea', compact('tiposTarea', 'jefe'));
    }

    /**
     * Guarda una nueva tarea en la base de datos.
     *
     * Después de persistir la tarea, crea un registro en 'historial_tareas'
     * con la acción 'Creada' para que el Comité FIFA pueda auditar.
     *
     * @param  Request  $request  Datos del formulario de creación.
     * @return RedirectResponse
     */
    public function storeTarea(Request $request): RedirectResponse
    {
        $jefe = Auth::user();

        // Validar todos los campos del formulario
        $validado = $request->validate([
            'titulo'        => ['required', 'string', 'max:255'],
            'descripcion'   => ['required', 'string'],
            'tipo_tarea_id' => ['required', 'exists:tipo_tarea,tipo_tarea_id'],
            'fecha_limite'  => ['required', 'date', 'after:today'],
            'estado'        => ['required', 'in:Pendiente,En Progreso'],
        ]);

        // Crear la tarea asociada al estadio del jefe
        $tarea = Tarea::create([
            'tipo_tarea_id' => $validado['tipo_tarea_id'],
            'estadio_id'    => $jefe->estadio_id,
            'creador_id'    => $jefe->user_id,
            'titulo'        => $validado['titulo'],
            'descripcion'   => $validado['descripcion'],
            'estado'        => $validado['estado'],
            'fecha_limite'  => $validado['fecha_limite'],
            'timestamp'     => now(),
        ]);

        // Registrar en el historial de auditoría
        HistorialTarea::create([
            'tarea_id'       => $tarea->tarea_id,
            'user_id'        => $jefe->user_id,
            'creador_id'     => $jefe->user_id,
            'accion'         => 'Creada',
            'estado_anterior' => null,
            'estado_nuevo'   => $tarea->estado,
            'detalle'        => 'Tarea "' . $tarea->titulo . '" creada por el jefe ' . $jefe->nombre,
            'timestamp'      => now(),
        ]);

        return redirect()->route('jefe.dashboard')
            ->with('success', 'Tarea "' . $tarea->titulo . '" creada exitosamente.');
    }

    /**
     * Muestra el formulario para asignar una tarea a técnicos.
     *
     * @param  int  $id  ID de la tarea a asignar.
     * @return View
     */
    public function asignarForm(int $id): View
    {
        $jefe = Auth::user();

        // Buscar la tarea (debe pertenecer al estadio del jefe)
        $tarea = Tarea::where('tarea_id', $id)
            ->where('estadio_id', $jefe->estadio_id)
            ->with('usuariosAsignados')
            ->firstOrFail();

        // Obtener solo los técnicos del mismo estadio
        $tecnicos = Usuario::where('estadio_id', $jefe->estadio_id)
            ->where('rol_id', 3)
            ->get();

        // IDs de técnicos ya asignados para pre-marcar checkboxes
        $tecnicosAsignados = $tarea->usuariosAsignados->pluck('user_id')->toArray();

        return view('jefe.asignar-tarea', compact('tarea', 'tecnicos', 'tecnicosAsignados'));
    }

    /**
     * Asigna una tarea a uno o más técnicos de campo.
     *
     * Evita duplicar asignaciones verificando si ya existe el registro.
     * Crea una entrada en historial por cada nueva asignación.
     *
     * @param  Request  $request  Contiene 'user_ids' array con IDs de técnicos.
     * @param  int      $id       ID de la tarea a asignar.
     * @return RedirectResponse
     */
    public function asignarTarea(Request $request, int $id): RedirectResponse
    {
        $jefe = Auth::user();

        // Validar que se seleccionó al menos un técnico
        $validado = $request->validate([
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['exists:usuarios,user_id'],
        ]);

        $tarea = Tarea::findOrFail($id);
        $asignacionesNuevas = 0;

        // Iterar sobre cada técnico seleccionado
        foreach ($validado['user_ids'] as $userId) {

            // Verificar que el técnico no esté ya asignado a esta tarea
            $yaAsignado = TareaUser::where('tarea_id', $id)
                ->where('user_id', $userId)
                ->exists();

            if (!$yaAsignado) {
                // Crear el registro de asignación en la tabla pivote
                TareaUser::create([
                    'tarea_id'         => $id,
                    'user_id'          => $userId,
                    'estado_asignacion' => 'Pendiente',
                    'fecha_asignacion' => now()->toDateString(),
                ]);

                // Crear registro de auditoría por cada asignación nueva
                HistorialTarea::create([
                    'tarea_id'        => $id,
                    'user_id'         => $userId,
                    'creador_id'      => $jefe->user_id,
                    'accion'          => 'Asignada',
                    'estado_anterior' => null,
                    'estado_nuevo'    => 'Pendiente',
                    'detalle'         => 'Tarea asignada al técnico ID ' . $userId
                                         . ' por el jefe ' . $jefe->nombre,
                    'timestamp'       => now(),
                ]);

                $asignacionesNuevas++;
            }
        }

        $mensaje = $asignacionesNuevas > 0
            ? "Tarea asignada a {$asignacionesNuevas} técnico(s) exitosamente."
            : 'Los técnicos seleccionados ya tenían esta tarea asignada.';

        return redirect()->route('jefe.dashboard')->with('success', $mensaje);
    }
}
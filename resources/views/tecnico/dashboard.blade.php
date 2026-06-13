{{--
    Vista: Dashboard del Técnico de Campo
    Muestra las tareas asignadas al técnico autenticado.
    Permite marcar tareas como completadas desde un modal de Bootstrap.
    Extiende el layout principal (layouts/app.blade.php).
--}}
@extends('layouts.app')

@section('title', 'Mis Tareas — Técnico')

@section('content')

{{-- ─── Encabezado de la sección ─────────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--sh-primary);">
            <i class="bi bi-tools me-2"></i>Mis Tareas Asignadas
        </h2>
        <p class="text-muted mb-0">
            Estadio: <strong>{{ auth()->user()->estadio->nombre ?? 'N/A' }}</strong>
        </p>
    </div>
    {{}}
</div>

{{-- ─── Tarjetas de estadísticas ──────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-list-task fs-1 text-primary"></i>
                <div>
                    <div class="stat-number">{{ $totalTareas }}</div>
                    <div class="text-muted small">Total de Tareas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 p-3" style="border-left-color: #ffc107;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                <div>
                    <div class="stat-number text-warning">{{ $tareasPendientes }}</div>
                    <div class="text-muted small">Pendientes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 p-3" style="border-left-color: #198754;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <div>
                    <div class="stat-number text-success">{{ $tareasCompletadas }}</div>
                    <div class="text-muted small">Completadas</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Tabla de Tareas Asignadas ──────────────────────────────────────── --}}
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-clipboard-check me-2"></i>Lista de Tareas
        </h5>
    </div>
    <div class="card-body p-0">
        @if($tareas->isEmpty())
            {{-- Mensaje cuando no hay tareas asignadas --}}
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">No tienes tareas asignadas por el momento.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-historial">
                    <thead class="table-light">
                        <tr>
                            <th>Tarea</th>
                            <th>Tipo</th>
                            <th>Fecha Límite</th>
                            <th>Estado</th>
                            <th>Creada Por</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Iterar sobre cada tarea asignada --}}
                        @foreach($tareas as $tarea)
                        <tr class="{{ $tarea->pivot->estado_asignacion === 'Completada' ? 'table-success' : '' }}">
                            <td>
                                <div class="fw-semibold">{{ $tarea->titulo }}</div>
                                <div class="text-muted small">{{ Str::limit($tarea->descripcion, 60) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $tarea->tipoTarea->nombre ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                {{-- Resaltar en rojo si la fecha límite ya pasó y no está completada --}}
                                @php
                                    $vencida = \Carbon\Carbon::parse($tarea->fecha_limite)->isPast()
                                               && $tarea->pivot->estado_asignacion !== 'Completada';
                                @endphp
                                <span class="{{ $vencida ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}
                                    @if($vencida) <i class="bi bi-exclamation-triangle-fill"></i> @endif
                                </span>
                            </td>
                            <td>
                                {{-- Badge de color según el estado de asignación --}}
                                @php
                                    $estado = $tarea->pivot->estado_asignacion;
                                    $badgeClass = match($estado) {
                                        'Completada'  => 'bg-success',
                                        'En Progreso' => 'bg-primary',
                                        default       => 'bg-warning text-dark', // Pendiente
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $tarea->creador->nombre ?? 'Sistema' }}
                            </td>
                            <td class="text-center">
                                @if($tarea->pivot->estado_asignacion !== 'Completada')
                                    {{-- Botón para abrir el modal de completar tarea --}}
                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalCompletar{{ $tarea->tarea_id }}">
                                        <i class="bi bi-check-lg"></i> Completar
                                    </button>
                                @else
                                    <span class="text-success small fw-semibold">
                                        <i class="bi bi-check-circle-fill"></i>
                                        {{ $tarea->pivot->fecha_completado
                                            ? \Carbon\Carbon::parse($tarea->pivot->fecha_completado)->format('d/m/Y')
                                            : 'Completada' }}
                                    </span>
                                @endif
                            </td>
                        </tr>

                        {{-- ─── Modal de confirmación para completar tarea ─── --}}
                        @if($tarea->pivot->estado_asignacion !== 'Completada')
                        <div class="modal fade" id="modalCompletar{{ $tarea->tarea_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            <i class="bi bi-check-circle me-2 text-success"></i>
                                            Completar Tarea
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    {{-- Formulario PATCH para completar la tarea --}}
                                    <form method="POST"
                                          action="{{ route('tecnico.tarea.completar', $tarea->tarea_id) }}">
                                        @csrf
                                        @method('PATCH') {{-- Simula método PATCH (HTML solo soporta GET/POST) --}}
                                        <div class="modal-body">
                                            <p>¿Confirmas que has completado la tarea:</p>
                                            <p class="fw-bold">{{ $tarea->titulo }}</p>
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Observaciones <span class="text-muted">(opcional)</span>
                                                </label>
                                                <textarea name="observaciones"
                                                          class="form-control"
                                                          rows="3"
                                                          placeholder="Notas sobre la ejecución de la tarea..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-circle me-1"></i>Confirmar Completado
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection
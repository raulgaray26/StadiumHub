{{--
    Vista: Dashboard del Técnico de Campo
    Muestra las tareas asignadas al técnico autenticado.
    Permite marcar tareas como completadas desde un modal de Bootstrap.
    Extiende el layout principal (layouts/app.blade.php).
--}}
@extends('layouts.app')
@section('title', 'Mis Tareas — Técnico')

@section('content')

{{-- Encabezado --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color:#1a1a2e;">
            <i class="bi bi-tools me-2"></i>Mis Tareas Asignadas
        </h2>
        <p class="text-muted mb-0">
            Estadio: <strong>{{ auth()->user()->estadio->nombre ?? 'N/A' }}</strong>
        </p>
    </div>
</div>

{{-- Tarjetas de estadísticas --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 h-100" style="border-left: 4px solid #1a1a2e;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-list-task fs-1" style="color:#1a1a2e;"></i>
                <div>
                    <div class="fw-bold fs-2" style="color:#1a1a2e;">{{ $totalTareas }}</div>
                    <div class="text-muted small">Total Asignadas</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100" style="border-left: 4px solid #ffc107;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-hourglass-split fs-1 text-warning"></i>
                <div>
                    <div class="fw-bold fs-2 text-warning">{{ $tareasPendientes }}</div>
                    <div class="text-muted small">Pendientes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100" style="border-left: 4px solid #198754;">
            <div class="d-flex align-items-center gap-3">
                <i class="bi bi-check-circle fs-1 text-success"></i>
                <div>
                    <div class="fw-bold fs-2 text-success">{{ $tareasCompletadas }}</div>
                    <div class="text-muted small">Completadas</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de tareas --}}
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-clipboard-check me-2"></i>Lista de Tareas
        </h5>
    </div>
    <div class="card-body p-0">

        @if($tareas->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2">No tienes tareas asignadas por el momento.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tarea</th>
                            <th>Tipo</th>
                            <th>Fecha Límite</th>
                            <th>Estado</th>
                            <th>Creada por</th>
                            <th class="text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tareas as $tarea)

                            {{-- Determinar si la tarea está vencida --}}
                            @php
                                $estadoAsig  = $tarea->pivot->estado_asignacion;
                                $completada  = ($estadoAsig === 'Completada');
                                $fechaLimite = \Carbon\Carbon::parse($tarea->fecha_limite);
                                $vencida     = $fechaLimite->isPast() && !$completada;
                            @endphp

                            <tr class="{{ $completada ? 'table-success' : '' }}">

                                <td>
                                    <div class="fw-semibold">{{ $tarea->titulo }}</div>
                                    <div class="text-muted small">
                                        {{ \Illuminate\Support\Str::limit($tarea->descripcion, 60) }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $tarea->tipoTarea->nombre ?? 'General' }}
                                    </span>
                                </td>

                                <td>
                                    @if($vencida)
                                        <span class="text-danger fw-bold">
                                            {{ $fechaLimite->format('d/m/Y') }}
                                            <i class="bi bi-exclamation-triangle-fill ms-1"></i>
                                        </span>
                                    @else
                                        {{ $fechaLimite->format('d/m/Y') }}
                                    @endif
                                </td>

                                <td>
                                    @if($estadoAsig === 'Completada')
                                        <span class="badge bg-success">Completada</span>
                                    @elseif($estadoAsig === 'En Progreso')
                                        <span class="badge bg-primary">En Progreso</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @endif
                                </td>

                                <td class="small text-muted">
                                    {{ $tarea->creador->nombre ?? 'Sistema' }}
                                </td>

                                <td class="text-center">
                                    @if(!$completada)
                                        <button type="button"
                                                class="btn btn-sm btn-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-{{ $tarea->tarea_id }}">
                                            <i class="bi bi-check-lg me-1"></i>Completar
                                        </button>
                                    @else
                                        <span class="text-success small">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            @if($tarea->pivot->fecha_completado)
                                                {{ \Carbon\Carbon::parse($tarea->pivot->fecha_completado)->format('d/m/Y') }}
                                            @else
                                                Completada
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            {{-- Modal de confirmación --}}
                            @if(!$completada)
                            <div class="modal fade" id="modal-{{ $tarea->tarea_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="bi bi-check-circle text-success me-2"></i>Completar Tarea
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST"
                                              action="{{ route('tecnico.tarea.completar', $tarea->tarea_id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <p class="mb-1">¿Confirmas que completaste:</p>
                                                <p class="fw-bold">{{ $tarea->titulo }}</p>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-semibold">
                                                        Observaciones <span class="text-muted">(opcional)</span>
                                                    </label>
                                                    <textarea name="observaciones"
                                                              class="form-control"
                                                              rows="3"
                                                              placeholder="Notas sobre la ejecución..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle me-1"></i>Confirmar
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
{{--
    Vista: Dashboard del Comité FIFA
    Centro de control global con estadísticas del sistema y log de auditoría.
    Solo accesible para usuarios con rol_id = 1.
--}}
@extends('layouts.app')
@section('title', 'Control Global — Comité FIFA')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color:#1a1a2e;">
        <i class="bi bi-bar-chart-line me-2"></i>Centro de Control Global
    </h2>
    <p class="text-muted mb-0">Auditoría FIFA 2026 — Todos los estadios sede</p>
</div>

{{-- ─── Estadísticas globales ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @php
        $stats = [
            ['label' => 'Estadios',     'value' => $totalEstadios,     'icon' => 'bi-building',       'color' => '#1a1a2e'],
            ['label' => 'Usuarios',     'value' => $totalUsuarios,      'icon' => 'bi-people-fill',    'color' => '#6f42c1'],
            ['label' => 'Tareas Total', 'value' => $totalTareas,        'icon' => 'bi-list-check',     'color' => '#0d6efd'],
            ['label' => 'Completadas',  'value' => $tareasCompletadas,  'icon' => 'bi-check2-all',     'color' => '#198754'],
            ['label' => 'Pendientes',   'value' => $tareasPendientes,   'icon' => 'bi-clock-history',  'color' => '#fd7e14'],
        ];
    @endphp

    @foreach($stats as $s)
    <div class="col-6 col-sm-4 col-md-2" style="min-width:150px;">
        <div class="card p-3 text-center h-100" style="border-left:4px solid {{ $s['color'] }};">
            <i class="bi {{ $s['icon'] }} fs-3 mb-1" style="color:{{ $s['color'] }};"></i>
            <div class="fw-bold fs-4" style="color:{{ $s['color'] }};">{{ $s['value'] }}</div>
            <div class="text-muted small">{{ $s['label'] }}</div>
        </div>
    </div>
    @endforeach

    {{-- Barra de progreso global --}}
    <div class="col">
        <div class="card p-3 h-100 d-flex justify-content-center">
            @php $pct = $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0; @endphp
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Progreso global</span>
                <span class="fw-bold">{{ $pct }}%</span>
            </div>
            <div class="progress mb-1" style="height:14px;">
                <div class="progress-bar bg-success" style="width:{{ $pct }}%;"></div>
            </div>
            <div class="text-muted small">{{ $tareasCompletadas }} / {{ $totalTareas }} tareas completadas</div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ─── Estado de estadios ──────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-building me-2"></i>Estado por Estadio
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($estadios as $est)
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="fw-semibold small">{{ $est->nombre }}</div>
                            <div class="text-muted" style="font-size:0.75rem;">
                                {{ $est->ciudad }}, {{ $est->pais }}
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-people me-1"></i>{{ $est->usuarios_count }}
                        </span>
                    </div>

                    @php
                        $pctEst = $est->tareas_count > 0
                            ? round(($est->tareas_completadas / $est->tareas_count) * 100)
                            : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="progress flex-grow-1" style="height:6px;">
                            <div class="progress-bar bg-success" style="width:{{ $pctEst }}%;"></div>
                        </div>
                        <small class="text-muted text-nowrap">
                            {{ $est->tareas_completadas }}/{{ $est->tareas_count }}
                        </small>
                    </div>

                    {{-- Botones de acción por estadio --}}
                    <div class="d-flex gap-2">
                        {{-- Ver todas las tareas del estadio --}}
                        <a href="{{ route('comite.estadio', $est->estadio_id) }}"
                           class="btn btn-sm btn-outline-dark flex-fill">
                            <i class="bi bi-list-task me-1"></i>Ver Tareas
                        </a>
                        {{-- Filtrar el historial por este estadio --}}
                        <a href="{{ route('comite.dashboard', ['estadio_id' => $est->estadio_id]) }}"
                           class="btn btn-sm btn-outline-secondary flex-fill">
                            <i class="bi bi-funnel me-1"></i>Historial
                        </a>
                    </div>
                </div>
                @empty
                    <div class="list-group-item text-center text-muted py-3">Sin estadios.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ─── Historial de auditoría ──────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold">
                        <i class="bi bi-journal-text me-2"></i>Historial de Auditoría
                        <span class="badge bg-secondary ms-1">{{ $historial->total() }}</span>
                    </h6>
                    {{-- Botón para limpiar filtros si hay alguno activo --}}
                    @if($filtroEstadio || $filtroAccion)
                        <a href="{{ route('comite.dashboard') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Quitar filtros
                        </a>
                    @endif
                </div>

                {{-- ── Filtros ──────────────────────────────────── --}}
                <form method="GET" action="{{ route('comite.dashboard') }}" class="row g-2 mt-2">
                    <div class="col-sm-5">
                        <select name="estadio_id" class="form-select form-select-sm">
                            <option value="">— Todos los estadios —</option>
                            @foreach($todosEstadios as $est)
                                <option value="{{ $est->estadio_id }}"
                                    {{ $filtroEstadio == $est->estadio_id ? 'selected' : '' }}>
                                    {{ $est->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <select name="accion" class="form-select form-select-sm">
                            <option value="">— Todas las acciones —</option>
                            @foreach($acciones as $accion)
                                <option value="{{ $accion }}"
                                    {{ $filtroAccion === $accion ? 'selected' : '' }}>
                                    {{ $accion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-sm btn-dark w-100">
                            <i class="bi bi-funnel me-1"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Acción</th>
                                <th>Tarea</th>
                                <th>Usuario</th>
                                <th class="text-center">Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historial as $reg)
                            <tr>
                                <td class="text-nowrap">
                                    {{ $reg->timestamp
                                        ? \Carbon\Carbon::parse($reg->timestamp)->format('d/m/Y H:i')
                                        : '—' }}
                                </td>
                                <td>
                                    @if($reg->accion === 'Completada')
                                        <span class="badge bg-success">{{ $reg->accion }}</span>
                                    @elseif($reg->accion === 'Asignada')
                                        <span class="badge bg-primary">{{ $reg->accion }}</span>
                                    @elseif($reg->accion === 'Creada')
                                        <span class="badge bg-info text-dark">{{ $reg->accion }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $reg->accion }}</span>
                                    @endif
                                </td>
                                <td>{{ $reg->tarea->titulo ?? '(eliminada)' }}</td>
                                <td>{{ $reg->usuario->nombre ?? '—' }}</td>
                                <td class="text-center">
                                    {{-- Botón que abre el modal con los detalles completos --}}
                                    <button type="button"
                                            class="btn btn-sm btn-outline-dark"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detalle-{{ $reg->historial_id }}">
                                        <i class="bi bi-eye me-1"></i>Ver
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    No hay registros con los filtros aplicados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($historial->hasPages())
            <div class="card-footer bg-white">
                {{ $historial->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Modales de detalle extraídos fuera de la estructura de la tabla para evitar que el backdrop de Bootstrap se congele --}}
@foreach($historial as $reg)
<div class="modal fade" id="detalle-{{ $reg->historial_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-journal-text me-2"></i>
                    Detalle del Registro #{{ $reg->historial_id }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th class="bg-light" style="width:35%;">Fecha / Hora</th>
                        <td>
                            {{ $reg->timestamp
                                ? \Carbon\Carbon::parse($reg->timestamp)->format('d/m/Y H:i:s')
                                : '—' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Acción</th>
                        <td>{{ $reg->accion }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tarea</th>
                        <td>{{ $reg->tarea->titulo ?? '(eliminada)' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Estadio</th>
                        <td>{{ $reg->tarea->estadio->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Actor</th>
                        <td>{{ $reg->usuario->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Iniciado por</th>
                        <td>{{ $reg->creador->nombre ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Estado anterior</th>
                        <td>{{ $reg->estado_anterior ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Estado nuevo</th>
                        <td>{{ $reg->estado_nuevo ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Detalle</th>
                        <td>{{ $reg->detalle ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
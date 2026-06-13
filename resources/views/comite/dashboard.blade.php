{{--
    Vista: Dashboard del Comité FIFA
    Centro de control global con estadísticas del sistema y log de auditoría.
    Solo accesible para usuarios con rol_id = 1.
--}}
@extends('layouts.app')
@section('title', 'Control Global — Comité FIFA')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold mb-1" style="color: var(--sh-primary);">
        <i class="bi bi-bar-chart-line me-2"></i>Centro de Control Global
    </h2>
    <p class="text-muted">
        Vista de auditoría FIFA 2026 — Todos los estadios sede
    </p>
</div>

{{-- ─── Estadísticas globales ────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Estadios Sede',  'value' => $totalEstadios,     'icon' => 'bi-building',        'color' => '#1a1a2e'],
        ['label' => 'Usuarios',       'value' => $totalUsuarios,      'icon' => 'bi-people-fill',     'color' => '#6f42c1'],
        ['label' => 'Total Tareas',   'value' => $totalTareas,        'icon' => 'bi-list-check',      'color' => '#0d6efd'],
        ['label' => 'Completadas',    'value' => $tareasCompletadas,  'icon' => 'bi-check2-all',      'color' => '#198754'],
        ['label' => 'Pendientes',     'value' => $tareasPendientes,   'icon' => 'bi-clock-history',   'color' => '#fd7e14'],
    ] as $stat)
    <div class="col-6 col-md-2 col-lg-2" style="min-width: 160px;">
        <div class="card p-3 stat-card h-100 text-center" style="border-left-color: {{ $stat['color'] }}">
            <i class="bi {{ $stat['icon'] }} fs-3 mb-1" style="color: {{ $stat['color'] }}"></i>
            <div class="stat-number" style="color: {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div class="text-muted small">{{ $stat['label'] }}</div>
        </div>
    </div>
    @endforeach

    {{-- Barra de progreso general --}}
    <div class="col-12 col-md">
        <div class="card p-3 h-100 d-flex justify-content-center">
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-muted">Progreso Global</span>
                <span class="fw-semibold">
                    {{ $totalTareas > 0 ? round(($tareasCompletadas / $totalTareas) * 100) : 0 }}%
                </span>
            </div>
            <div class="progress" style="height: 12px;">
                <div class="progress-bar bg-success"
                     style="width: {{ $totalTareas > 0 ? ($tareasCompletadas / $totalTareas) * 100 : 0 }}%">
                </div>
            </div>
            <div class="text-muted small mt-1">
                {{ $tareasCompletadas }} de {{ $totalTareas }} tareas completadas
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ─── Log de Auditoría ──────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-journal-text me-2"></i>Historial de Auditoría
                </h6>
                <span class="badge bg-secondary">{{ $historial->total() }} registros</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-historial">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Acción</th>
                                <th>Tarea</th>
                                <th>Usuario</th>
                                <th>Iniciado Por</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($historial as $registro)
                            <tr>
                                <td class="small text-nowrap">
                                    {{ $registro->timestamp
                                        ? \Carbon\Carbon::parse($registro->timestamp)->format('d/m/Y H:i')
                                        : 'N/A' }}
                                </td>
                                <td>
                                    @php
                                        $accionClass = match($registro->accion) {
                                            'Completada' => 'bg-success',
                                            'Asignada'   => 'bg-primary',
                                            'Creada'     => 'bg-info text-dark',
                                            'Editada'    => 'bg-warning text-dark',
                                            default      => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $accionClass }}">{{ $registro->accion }}</span>
                                </td>
                                <td class="small">
                                    {{ $registro->tarea->titulo ?? '(eliminada)' }}
                                </td>
                                <td class="small">
                                    {{ $registro->usuario->nombre ?? 'N/A' }}
                                </td>
                                <td class="small">
                                    {{ $registro->creador->nombre ?? 'Sistema' }}
                                </td>
                                <td class="small text-muted">
                                    {{ Str::limit($registro->detalle, 50) }}
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No hay registros en el historial.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- Paginación de Laravel con Bootstrap --}}
            @if($historial->hasPages())
            <div class="card-footer bg-white">
                {{ $historial->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

    {{-- ─── Estado de Estadios ─────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-building me-2"></i>Estado por Estadio
                </h6>
            </div>
            <div class="list-group list-group-flush">
                @forelse($estadios as $estadio)
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="fw-semibold small">{{ $estadio->nombre }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                {{ $estadio->ciudad }}, {{ $estadio->pais }}
                            </div>
                        </div>
                        <div class="text-end small">
                            <span class="text-muted">{{ $estadio->usuarios_count }}
                                <i class="bi bi-person"></i>
                            </span>
                        </div>
                    </div>
                    {{-- Barra de progreso de tareas por estadio --}}
                    @php
                        $porcentaje = $estadio->tareas_count > 0
                            ? round(($estadio->tareas_completadas / $estadio->tareas_count) * 100)
                            : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <small class="text-muted text-nowrap">
                            {{ $estadio->tareas_completadas }}/{{ $estadio->tareas_count }}
                        </small>
                    </div>
                </div>
                @empty
                    <div class="list-group-item text-center text-muted py-3">
                        Sin estadios registrados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
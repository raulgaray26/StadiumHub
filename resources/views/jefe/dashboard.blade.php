{{--
    Vista: Dashboard del Jefe de Mantenimiento
    Muestra estadísticas del estadio, lista de tareas y técnicos disponibles.
--}}
@extends('layouts.app')
@section('title', 'Panel de Gestión — Jefe')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1" style="color: var(--sh-primary);">
            <i class="bi bi-clipboard-check me-2"></i>Panel de Gestión
        </h2>
        <p class="text-muted mb-0">
            Estadio: <strong>{{ auth()->user()->estadio->nombre ?? 'N/A' }}</strong>
            &mdash; {{ auth()->user()->estadio->ciudad ?? '' }}, {{ auth()->user()->estadio->pais ?? '' }}
        </p>
    </div>
    <a href="{{ route('jefe.tarea.crear') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nueva Tarea
    </a>
</div>

{{-- ─── Tarjetas de estadísticas ──────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Total Tareas',  'value' => $totalTareas,       'icon' => 'bi-list-task',          'color' => '#1a1a2e'],
        ['label' => 'Pendientes',    'value' => $tareasPendientes,   'icon' => 'bi-hourglass-split',    'color' => '#ffc107'],
        ['label' => 'En Progreso',   'value' => $tareasEnProgreso,   'icon' => 'bi-arrow-repeat',       'color' => '#0d6efd'],
        ['label' => 'Completadas',   'value' => $tareasCompletadas,  'icon' => 'bi-check-circle-fill',  'color' => '#198754'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card p-3 stat-card h-100" style="border-left-color: {{ $stat['color'] }}">
            <div class="d-flex align-items-center gap-3">
                <i class="bi {{ $stat['icon'] }} fs-2" style="color: {{ $stat['color'] }}"></i>
                <div>
                    <div class="stat-number" style="color: {{ $stat['color'] }}">{{ $stat['value'] }}</div>
                    <div class="text-muted small">{{ $stat['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- ─── Tabla de Tareas ──────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-table me-2"></i>Tareas del Estadio</h6>
            </div>
            <div class="card-body p-0">
                @if($tareas->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No hay tareas registradas. ¡Crea la primera!</p>
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
                                <th>Asignados</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tareas as $tarea)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $tarea->titulo }}</div>
                                    <div class="text-muted small">{{ Str::limit($tarea->descripcion, 50) }}</div>
                                </td>
                                <td><span class="badge bg-secondary">{{ $tarea->tipoTarea->nombre ?? 'N/A' }}</span></td>
                                <td class="small">
                                    {{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($tarea->estado) {
                                            'Completada'  => 'bg-success',
                                            'En Progreso' => 'bg-primary',
                                            default       => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $tarea->estado }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $tarea->usuariosAsignados->count() }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('jefe.tarea.asignar', $tarea->tarea_id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-person-plus"></i> Asignar
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ─── Técnicos del Estadio ──────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-people me-2"></i>Técnicos Disponibles
                    <span class="badge bg-secondary ms-1">{{ $tecnicos->count() }}</span>
                </h6>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($tecnicos as $tecnico)
                    <li class="list-group-item d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-person-circle fs-4 text-muted"></i>
                        <div>
                            <div class="fw-semibold small">{{ $tecnico->nombre }}</div>
                            <div class="text-muted" style="font-size: 0.78rem;">{{ $tecnico->email }}</div>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted text-center py-3">
                        No hay técnicos en este estadio.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

@endsection
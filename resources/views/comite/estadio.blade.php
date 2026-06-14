@extends('layouts.app')
@section('title', 'Tareas — ' . $estadio->nombre)

@section('content')

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('comite.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h3 class="fw-bold mb-0" style="color:#1a1a2e;">
            <i class="bi bi-building me-2"></i>{{ $estadio->nombre }}
        </h3>
        <p class="text-muted mb-0 small">
            {{ $estadio->ciudad }}, {{ $estadio->pais }} —
            Capacidad: {{ number_format($estadio->capacidad) }} espectadores
        </p>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3 d-flex justify-content-between">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-list-task me-2"></i>Todas las Tareas
            <span class="badge bg-secondary ms-1">{{ $tareas->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        @if($tareas->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">No hay tareas registradas para este estadio.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle small">
                <thead class="table-light">
                    <tr>
                        <th>Tarea</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Fecha Límite</th>
                        <th>Creada por</th>
                        <th>Técnicos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tareas as $tarea)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $tarea->titulo }}</div>
                            <div class="text-muted">
                                {{ \Illuminate\Support\Str::limit($tarea->descripcion, 70) }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $tarea->tipoTarea->nombre ?? 'General' }}
                            </span>
                        </td>
                        <td>
                            @if($tarea->estado === 'Completada')
                                <span class="badge bg-success">Completada</span>
                            @elseif($tarea->estado === 'En Progreso')
                                <span class="badge bg-primary">En Progreso</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}</td>
                        <td>{{ $tarea->creador->nombre ?? '—' }}</td>
                        <td>
                            @forelse($tarea->usuariosAsignados as $tec)
                                <span class="badge bg-light text-dark border me-1">
                                    {{ $tec->nombre }}
                                    @if($tec->pivot->estado_asignacion === 'Completada')
                                        <i class="bi bi-check-circle-fill text-success ms-1"></i>
                                    @endif
                                </span>
                            @empty
                                <span class="text-muted">Sin asignar</span>
                            @endforelse
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@endsection
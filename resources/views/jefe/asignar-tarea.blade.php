@extends('layouts.app')
@section('title', 'Asignar Tarea — Jefe')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-6">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('jefe.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold mb-0" style="color: var(--sh-primary);">
                <i class="bi bi-person-plus me-2"></i>Asignar Técnicos
            </h3>
        </div>

        {{-- Información de la tarea que se va a asignar --}}
        <div class="card border-primary mb-4">
            <div class="card-header bg-primary text-white py-2">
                <strong><i class="bi bi-info-circle me-1"></i>Tarea a Asignar</strong>
            </div>
            <div class="card-body py-3">
                <h5 class="fw-bold">{{ $tarea->titulo }}</h5>
                <p class="text-muted small mb-1">{{ $tarea->descripcion }}</p>
                <div class="d-flex gap-3 small">
                    <span><i class="bi bi-tag me-1"></i>{{ $tarea->tipoTarea->nombre ?? 'N/A' }}</span>
                    <span><i class="bi bi-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($tarea->fecha_limite)->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Formulario de selección de técnicos --}}
        <div class="card p-4">
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-people me-2"></i>Seleccionar Técnicos del Estadio
            </h6>

            @if($tecnicos->isEmpty())
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    No hay técnicos registrados en este estadio.
                </div>
            @else
                <form method="POST" action="{{ route('jefe.tarea.asignar.store', $tarea->tarea_id) }}">
                    @csrf

                    @error('user_ids')
                        <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
                    @enderror

                    {{-- Lista de checkboxes con cada técnico --}}
                    @foreach($tecnicos as $tecnico)
                    <div class="form-check border rounded p-3 mb-2
                                {{ in_array($tecnico->user_id, $tecnicosAsignados) ? 'bg-light' : '' }}">
                        <input type="checkbox"
                               class="form-check-input"
                               name="user_ids[]"
                               id="tecnico_{{ $tecnico->user_id }}"
                               value="{{ $tecnico->user_id }}"
                               {{ in_array($tecnico->user_id, $tecnicosAsignados) ? 'checked' : '' }}>
                        <label class="form-check-label d-flex align-items-center gap-2"
                               for="tecnico_{{ $tecnico->user_id }}">
                            <i class="bi bi-person-circle fs-4 text-muted"></i>
                            <div>
                                <div class="fw-semibold">{{ $tecnico->nombre }}</div>
                                <div class="text-muted small">{{ $tecnico->email }}</div>
                                @if(in_array($tecnico->user_id, $tecnicosAsignados))
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        Ya asignado
                                    </span>
                                @endif
                            </div>
                        </label>
                    </div>
                    @endforeach

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Confirmar Asignación
                        </button>
                        <a href="{{ route('jefe.dashboard') }}" class="btn btn-outline-secondary">
                            Cancelar
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
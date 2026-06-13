@extends('layouts.app')
@section('title', 'Nueva Tarea — Jefe')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">

        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('jefe.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold mb-0" style="color: var(--sh-primary);">
                <i class="bi bi-plus-circle me-2"></i>Nueva Tarea de Mantenimiento
            </h3>
        </div>

        <div class="card p-4">
            <form method="POST" action="{{ route('jefe.tarea.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="titulo" class="form-label fw-semibold">Título de la Tarea</label>
                    <input type="text" id="titulo" name="titulo"
                           class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo') }}"
                           placeholder="Ej: Riego zona norte sector 3"
                           required>
                    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label fw-semibold">Descripción Detallada</label>
                    <textarea id="descripcion" name="descripcion" rows="4"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              placeholder="Instrucciones específicas para el técnico..."
                              required>{{ old('descripcion') }}</textarea>
                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <label for="tipo_tarea_id" class="form-label fw-semibold">Tipo de Tarea</label>
                        <select id="tipo_tarea_id" name="tipo_tarea_id"
                                class="form-select @error('tipo_tarea_id') is-invalid @enderror" required>
                            <option value="">— Seleccionar tipo —</option>
                            @foreach($tiposTarea as $tipo)
                                <option value="{{ $tipo->tipo_tarea_id }}"
                                        {{ old('tipo_tarea_id') == $tipo->tipo_tarea_id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_tarea_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <label for="estado" class="form-label fw-semibold">Estado Inicial</label>
                        <select id="estado" name="estado" class="form-select" required>
                            <option value="Pendiente" {{ old('estado') == 'Pendiente' ? 'selected' : '' }}>
                                Pendiente
                            </option>
                            <option value="En Progreso" {{ old('estado') == 'En Progreso' ? 'selected' : '' }}>
                                En Progreso
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="fecha_limite" class="form-label fw-semibold">Fecha Límite</label>
                    <input type="date" id="fecha_limite" name="fecha_limite"
                           class="form-control @error('fecha_limite') is-invalid @enderror"
                           value="{{ old('fecha_limite') }}"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           required>
                    @error('fecha_limite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Estadio pre-rellenado (no editable, viene del jefe) --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Estadio Asociado</label>
                    <input type="text" class="form-control bg-light"
                           value="{{ $jefe->estadio->nombre ?? 'N/A' }} — {{ $jefe->estadio->ciudad ?? '' }}"
                           disabled>
                    <div class="form-text">Las tareas se crean para tu estadio asignado.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Crear Tarea
                    </button>
                    <a href="{{ route('jefe.dashboard') }}" class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
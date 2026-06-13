{{--
    Vista: Registro
    Formulario para crear nuevos usuarios en el sistema.
    El usuario debe seleccionar obligatoriamente su Rol y Estadio.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StadiumHub — Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-wrapper py-5">
    <div class="auth-card bg-white shadow-lg" style="max-width: 520px;">

        {{-- ─── Encabezado ────────────────────────────────────────── --}}
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: var(--sh-primary);">
                <i class="bi bi-person-plus me-2"></i>Crear Cuenta
            </h2>
            <p class="text-muted small">StadiumHub — FIFA 2026 Maintenance System</p>
        </div>

        {{-- ─── Errores de validación ──────────────────────────────── --}}
        @if($errors->any())
            <div class="alert alert-danger py-2">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ─── Formulario de Registro ────────────────────────────── --}}
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            {{-- Campo Nombre completo --}}
            <div class="mb-3">
                <label for="nombre" class="form-label fw-semibold">
                    <i class="bi bi-person me-1"></i>Nombre Completo
                </label>
                <input type="text"
                       id="nombre"
                       name="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre') }}"
                       placeholder="Ej: Juan Pérez García"
                       required>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Campo Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i>Correo Electrónico
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="usuario@fifa.com"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Campos Contraseña en dos columnas --}}
            <div class="row mb-3">
                <div class="col-6">
                    <label for="password" class="form-label fw-semibold">
                        <i class="bi bi-lock me-1"></i>Contraseña
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Mín. 8 caracteres"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label for="password_confirmation" class="form-label fw-semibold">
                        Confirmar
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Repetir contraseña"
                           required>
                </div>
            </div>

            {{-- Selector de Rol (OBLIGATORIO) --}}
            <div class="mb-3">
                <label for="rol_id" class="form-label fw-semibold">
                    <i class="bi bi-shield-check me-1"></i>Rol en el Sistema
                    <span class="text-danger">*</span>
                </label>
                <select id="rol_id"
                        name="rol_id"
                        class="form-select @error('rol_id') is-invalid @enderror"
                        required>
                    <option value="">— Seleccionar rol —</option>
                    {{-- $roles viene del método showRegister() del AuthController --}}
                    @foreach($roles as $rol)
                        <option value="{{ $rol->rol_id }}"
                                {{ old('rol_id') == $rol->rol_id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('rol_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Selector de Estadio Asignado --}}
            <div class="mb-4">
                <label for="estadio_id" class="form-label fw-semibold">
                    <i class="bi bi-building me-1"></i>Estadio Asignado
                    <span class="text-danger">*</span>
                </label>
                <select id="estadio_id"
                        name="estadio_id"
                        class="form-select @error('estadio_id') is-invalid @enderror"
                        required>
                    <option value="">— Seleccionar estadio —</option>
                    {{-- $estadios viene del método showRegister() del AuthController --}}
                    @foreach($estadios as $estadio)
                        <option value="{{ $estadio->estadio_id }}"
                                {{ old('estadio_id') == $estadio->estadio_id ? 'selected' : '' }}>
                            {{ $estadio->nombre }} — {{ $estadio->ciudad }}, {{ $estadio->pais }}
                        </option>
                    @endforeach
                </select>
                @error('estadio_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Botón de envío --}}
            <button type="submit" class="btn btn-lg w-100 text-white fw-semibold"
                    style="background-color: var(--sh-primary);">
                <i class="bi bi-check-circle me-2"></i>Crear Mi Cuenta
            </button>
        </form>

        {{-- Enlace al login --}}
        <hr class="my-4">
        <p class="text-center text-muted mb-0">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                Iniciar sesión
            </a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
{{--
    Vista: Login
    Muestra el formulario de inicio de sesión de StadiumHub.
    No extiende el layout principal porque tiene su propio diseño centrado.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StadiumHub — Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card bg-white shadow-lg">

        {{-- ─── Logo / Encabezado ─────────────────────────────────── --}}
        <div class="text-center mb-4">
            {{--
                📁 IMAGEN: logo en public/images/logo-login.png
            --}}
            <img src="{{ asset('images/logo-login.png') }}"
                 alt="StadiumHub"
                 height="70"
                 class="mb-3"
                 onerror="this.style.display='none'">
            <h2 class="fw-bold" style="color: var(--sh-primary);">StadiumHub</h2>
            <p class="text-muted small">FIFA World Cup 2026 — Maintenance System</p>
        </div>

        {{-- ─── Errores de validación ──────────────────────────────── --}}
        @if($errors->any())
            <div class="alert alert-danger py-2">
                @foreach($errors->all() as $error)
                    <div><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- ─── Formulario de Login ───────────────────────────────── --}}
        {{-- method POST para no exponer credenciales en la URL --}}
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf {{-- Token CSRF obligatorio en formularios POST de Laravel --}}

            {{-- Campo Email --}}
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">
                    <i class="bi bi-envelope me-1"></i>Correo Electrónico
                </label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control form-control-lg @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="usuario@fifa.com"
                       autocomplete="email"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Campo Contraseña --}}
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">
                    <i class="bi bi-lock me-1"></i>Contraseña
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       autocomplete="current-password"
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Opción "Recuérdame" --}}
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label text-muted" for="remember">Mantener sesión iniciada</label>
            </div>

            {{-- Botón de envío --}}
            <button type="submit" class="btn btn-lg w-100 text-white fw-semibold"
                    style="background-color: var(--sh-primary);">
                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar al Sistema
            </button>
        </form>

        {{-- ─── Enlace al Registro ─────────────────────────────────── --}}
        <hr class="my-4">
        <p class="text-center text-muted mb-0">
            ¿Nuevo usuario?
            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold">
                Crear cuenta
            </a>
        </p>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
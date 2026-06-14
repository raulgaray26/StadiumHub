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
    <link rel="icon" href="{{ asset('images/logo-login.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card { width: 100%; max-width: 420px; border-radius: 16px; padding: 2.5rem; }
    </style>
</head>
<body>

<div class="auth-card bg-white shadow-lg">

    <div class="text-center mb-4">
        <div class="mb-2">
            <img src="{{ asset('images/logo-login.png') }}" alt="Logo de StadiumHub" style="max-height: 80px; width: auto;">
        </div>
        <h3 class="fw-bold" style="color:#1a1a2e;">StadiumHub</h3>
        <p class="text-muted small mb-0">FIFA World Cup 2026 — Sistema de Mantenimiento</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            @foreach($errors->all() as $error)
                <div class="small"><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success py-2 mb-3 small">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

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
                   required autofocus>
        </div>

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
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label text-muted small" for="remember">
                Mantener sesión iniciada
            </label>
        </div>

        {{-- Botón de inicio de sesión --}}
        <button type="submit"
                class="btn btn-lg w-100 fw-semibold text-white"
                style="background-color: #1a1a2e; border-color: #1a1a2e;">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
        </button>
    </form>

    <hr class="my-4">
    <p class="text-center text-muted small mb-0">
        ¿Nuevo usuario?
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none">
            Crear cuenta
        </a>
    </p>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
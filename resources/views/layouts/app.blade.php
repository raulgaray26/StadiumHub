<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Título dinámico: cada vista puede definir su propio título con @section('title') --}}
    <title>StadiumHub — @yield('title', 'Panel de Control')</title>

    {{-- Bootstrap 5.3 CSS (CDN) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    {{-- Bootstrap Icons (para íconos en la interfaz) --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Estilos personalizados de StadiumHub --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Stack para estilos adicionales por vista --}}
    @stack('styles')
</head>
<body class="bg-light">

    {{-- ═══ BARRA DE NAVEGACIÓN PRINCIPAL ═══════════════════════════════ --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #1a1a2e;">
        <div class="container-fluid">

            {{-- Logo / Nombre del sistema --}}
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                {{--
                    📁 IMAGEN: logo en public/images/logo-stadiumhub.png
                --}}
                <img src="{{ asset('images/logo-stadiumhub.png') }}"
                     alt="StadiumHub Logo"
                     width="32" height="32"
                     onerror="this.style.display='none'">
                <strong>StadiumHub</strong>
            </a>

            {{-- Botón hamburguesa para pantallas pequeñas --}}
            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                {{-- Ítems de navegación según el rol --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(auth()->user()->esTecnico())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tecnico.dashboard') ? 'active' : '' }}"
                               href="{{ route('tecnico.dashboard') }}">
                                <i class="bi bi-tools"></i> Mis Tareas
                            </a>
                        </li>
                    @elseif(auth()->user()->esJefe())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jefe.dashboard') ? 'active' : '' }}"
                               href="{{ route('jefe.dashboard') }}">
                                <i class="bi bi-clipboard-check"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('jefe.tarea.crear') ? 'active' : '' }}"
                               href="{{ route('jefe.tarea.crear') }}">
                                <i class="bi bi-plus-circle"></i> Nueva Tarea
                            </a>
                        </li>
                    @elseif(auth()->user()->esComite())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('comite.dashboard') ? 'active' : '' }}"
                               href="{{ route('comite.dashboard') }}">
                                <i class="bi bi-bar-chart-line"></i> Auditoría Global
                            </a>
                        </li>
                    @endif
                </ul>

                {{-- Información del usuario autenticado --}}
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-1"
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span>{{ auth()->user()->nombre }}</span>
                            <span class="badge bg-secondary ms-1">
                                {{ auth()->user()->rol->nombre ?? 'Sin Rol' }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text text-muted small">
                                    {{ auth()->user()->email }}
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                {{-- Formulario de logout (POST para seguridad CSRF) --}}
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    {{-- ═══ CONTENIDO PRINCIPAL ══════════════════════════════════════════ --}}
    <main class="container py-4">

        {{-- Mensajes flash de éxito --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Mensajes flash de error --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Errores de validación globales --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Contenido específico de cada vista --}}
        @yield('content')
    </main>

    {{-- ═══ FOOTER ════════════════════════════════════════════════════════ --}}
    <footer class="footer py-3 mt-5 border-top bg-white">
        <div class="container text-center text-muted small">
            <span>StadiumHub &copy; {{ date('Y') }}</span>
            &middot;
            <span>FIFA World Cup 2026 — Stadium Maintenance System</span>
        </div>
    </footer>

    {{-- Bootstrap 5.3 JS Bundle (incluye Popper.js) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmRYFHQKJ/HZjcW6RDo5Cq5yBi"
            crossorigin="anonymous"></script>

    {{-- Stack para scripts adicionales por vista --}}
    @stack('scripts')
</body>
</html>
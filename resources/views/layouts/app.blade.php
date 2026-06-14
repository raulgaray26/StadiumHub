<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>StadiumHub — @yield('title', 'Panel de Control')</title>

    {{-- Bootstrap 5.3 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Estilos propios --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body class="bg-light">

@auth
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#1a1a2e;">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-building me-1"></i> StadiumHub
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMain"
                aria-controls="navbarMain" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                @if(auth()->user()->esTecnico())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('tecnico.dashboard') ? 'active' : '' }}"
                           href="{{ route('tecnico.dashboard') }}">
                            <i class="bi bi-tools me-1"></i>Mis Tareas
                        </a>
                    </li>

                @elseif(auth()->user()->esJefe())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('jefe.dashboard') ? 'active' : '' }}"
                           href="{{ route('jefe.dashboard') }}">
                            <i class="bi bi-clipboard-check me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('jefe.tarea.crear') ? 'active' : '' }}"
                           href="{{ route('jefe.tarea.crear') }}">
                            <i class="bi bi-plus-circle me-1"></i>Nueva Tarea
                        </a>
                    </li>

                @elseif(auth()->user()->esComite())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('comite.*') ? 'active' : '' }}"
                           href="{{ route('comite.dashboard') }}">
                            <i class="bi bi-bar-chart-line me-1"></i>Auditoría
                        </a>
                    </li>
                @endif

            </ul>

            {{-- Dropdown del usuario (logout) --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    {{-- trigger: data-bs-toggle="dropdown" requiere Bootstrap JS cargado --}}
                    <a class="nav-link dropdown-toggle" href="#"
                       id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->nombre }}
                        <span class="badge bg-secondary ms-1" style="font-size:0.7rem;">
                            {{ auth()->user()->rol->nombre ?? '' }}
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                {{ auth()->user()->email }}
                            </span>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
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

<main class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="py-3 mt-4 border-top bg-white text-center text-muted small">
    StadiumHub &copy; {{ date('Y') }} — FIFA World Cup 2026 Maintenance System
</footer>

{{-- Bootstrap 5.3 JS Bundle — sin atributo integrity para evitar bloqueos SRI --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
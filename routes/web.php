<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| StadiumHub — Definición de Rutas Web
|--------------------------------------------------------------------------
|
| Estructura de rutas:
|   / → Redirige al login
|   /login → Formulario de inicio de sesión
|   /registro → Formulario de registro
|   /logout → Cierre de sesión (POST)
|   /tecnico/* → Área del Técnico de Campo (auth + rol.tecnico)
|   /jefe/* → Área del Jefe de Mantenimiento (auth + rol.jefe)
|   /comite/* → Área del Comité FIFA (auth + rol.comite)
|
| NOTA: Las rutas de /tecnico, /jefe y /comite serán completadas
|       por el Miembro 2 en sus respectivas ramas de feature.
*/

// ─── RUTA RAÍZ ────────────────────────────────────────────────────────────────
// Si el usuario accede a "/", redirigir al login
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── RUTAS DE AUTENTICACIÓN (sin middleware, accesibles para todos) ────────────
Route::middleware('guest')->group(function () {
    // Mostrar formulario de login
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    // Procesar intento de login (POST)
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.submit');

    // Mostrar formulario de registro
    Route::get('/registro', [AuthController::class, 'showRegister'])
        ->name('register');

    // Procesar registro de nuevo usuario (POST)
    Route::post('/registro', [AuthController::class, 'register'])
        ->name('register.submit');
});

// Cerrar sesión (requiere estar autenticado)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ─── RUTAS DEL TÉCNICO DE CAMPO ───────────────────────────────────────────────
Route::middleware(['auth', 'rol.tecnico'])
    ->prefix('tecnico')
    ->name('tecnico.')
    ->group(function () {

        // Dashboard principal: lista de tareas asignadas
        Route::get('/dashboard', [\App\Http\Controllers\TecnicoController::class, 'dashboard'])
            ->name('dashboard');

        // Marcar tarea como completada (PATCH = actualización parcial semántica)
        Route::patch('/tarea/{id}/completar', [\App\Http\Controllers\TecnicoController::class, 'completarTarea'])
            ->name('tarea.completar');
    });

// ─── RUTAS DEL JEFE DE MANTENIMIENTO ─────────────────────────────────────────
Route::middleware(['auth', 'rol.jefe'])
    ->prefix('jefe')
    ->name('jefe.')
    ->group(function () {

        // Panel principal del jefe
        Route::get('/dashboard', [\App\Http\Controllers\JefeController::class, 'dashboard'])
            ->name('dashboard');

        // Mostrar formulario de creación de tarea
        Route::get('/tarea/crear', [\App\Http\Controllers\JefeController::class, 'crearTareaForm'])
            ->name('tarea.crear');

        // Guardar nueva tarea (POST)
        Route::post('/tarea/crear', [\App\Http\Controllers\JefeController::class, 'storeTarea'])
            ->name('tarea.store');

        // Mostrar formulario de asignación de técnicos
        Route::get('/tarea/{id}/asignar', [\App\Http\Controllers\JefeController::class, 'asignarForm'])
            ->name('tarea.asignar');

        // Procesar asignación (POST)
        Route::post('/tarea/{id}/asignar', [\App\Http\Controllers\JefeController::class, 'asignarTarea'])
            ->name('tarea.asignar.store');
    });

// ─── RUTAS DEL COMITÉ FIFA ────────────────────────────────────────────────────
Route::middleware(['auth', 'rol.comite'])
    ->prefix('comite')
    ->name('comite.')
    ->group(function () {

        // Dashboard global de auditoría
        Route::get('/dashboard', [\App\Http\Controllers\ComiteController::class, 'dashboard'])
            ->name('dashboard');

        // Detalle de tareas por estadio
        Route::get('/estadio/{id}', [\App\Http\Controllers\ComiteController::class, 'verEstadio'])
            ->name('estadio');
    });
 
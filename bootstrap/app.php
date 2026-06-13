<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /*
        |----------------------------------------------------------------------
        | Alias de Middlewares Personalizados de StadiumHub
        |----------------------------------------------------------------------
        | Registramos nuestros tres middlewares de rol con nombres cortos
        | para poder usarlos fácilmente en las rutas:
        |   ->middleware('rol.comite')
        |   ->middleware('rol.jefe')
        |   ->middleware('rol.tecnico')
        */
        $middleware->alias([
            'rol.comite'  => \App\Http\Middleware\EsComite::class,
            'rol.jefe'    => \App\Http\Middleware\EsJefe::class,
            'rol.tecnico' => \App\Http\Middleware\EsTecnico::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
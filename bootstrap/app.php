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
    ->withMiddleware(function (Middleware $middleware): void {
        // Giriş yapmamış kullanıcılar yönetim girişine, giriş yapmışlar panele yönlenir
        $middleware->redirectGuestsTo(fn () => route('yonetim.giris'));
        $middleware->redirectUsersTo(fn () => route('yonetim.panel'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

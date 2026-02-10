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

        // Route middleware aliases (Laravel 12 style)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'pro' => \App\Http\Middleware\ProUser::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,

            // ⭐ ADDED ONLY THIS (for onboarding)
            'onboarded' => \App\Http\Middleware\EnsureOnboarded::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

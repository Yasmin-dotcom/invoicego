<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Compatibility Kernel.
 *
 * Laravel 12 primarily registers middleware aliases in `bootstrap/app.php`.
 * This file exists to match conventional Laravel expectations and keep the
 * app extensible for packages/tools that look for App\Http\Kernel.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string>
     */
    protected $middlewareAliases = [
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'client' => \App\Http\Middleware\ClientMiddleware::class,
        'onboarded' => \App\Http\Middleware\EnsureOnboarded::class,
        'pro' => \App\Http\Middleware\ProUser::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}


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
     * The application's middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

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


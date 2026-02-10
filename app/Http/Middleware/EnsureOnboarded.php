<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboarded
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();
        if ($user && ! $user->onboarded) {
            return redirect()->route('onboarding');
        }

        return $next($request);
    }
}

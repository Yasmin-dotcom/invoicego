<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        if (($user->plan ?? 'free') !== 'pro') {
            return redirect('/upgrade')
                ->with('error', 'Upgrade to Pro to access this feature');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Not logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Not client → HARD BLOCK
        if (Auth::user()->role !== 'client') {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}

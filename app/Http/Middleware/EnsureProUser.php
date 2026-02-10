<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (($request->user()->plan ?? 'free') !== 'pro') {
            return redirect('/upgrade')
                ->with('error', 'Upgrade to Pro to access this feature');
        }

        return $next($request);
    }
}


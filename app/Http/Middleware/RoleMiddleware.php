<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $currentRole = $user->role ?? null;

        // Backward compatible: "owner" is the new name for legacy "client" users.
        $allowedRoles = [$role];
        if (in_array($role, ['client', 'owner'], true)) {
            $allowedRoles = ['client', 'owner'];
        }

        if (! in_array($currentRole, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}


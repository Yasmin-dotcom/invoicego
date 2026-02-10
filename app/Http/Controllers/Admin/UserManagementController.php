<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        $role   = $request->role;
        $plan   = $request->plan;

        $users = User::query()
            ->select(['id', 'name', 'email', 'role', 'plan', 'created_at'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($q) use ($role) {
                // Backward compatible: "owner" includes legacy "client" rows until migrations are applied.
                if ($role === 'owner') {
                    return $q->whereIn('role', ['owner', 'client']);
                }
                return $q->where('role', $role);
            })
            ->when($plan, fn ($q) => $q->where('plan', $plan))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function updatePlan(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();

        if (!$actor || ($actor->role ?? null) !== 'admin') {
            abort(403);
        }

        // Safety: never allow changing an admin user's plan, or your own plan.
        if (($user->role ?? null) === 'admin' || $user->id === $actor->id) {
            abort(403);
        }

        $validated = $request->validate([
            'plan' => ['required', 'in:free,pro'],
        ]);

        $plan = $validated['plan'];

        if ($user->plan !== $plan) {
            $user->forceFill(['plan' => $plan])->save();
        }

        return response()->json([
            'success' => true,
            'plan' => $user->plan,
        ]);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $actor = $request->user();
        if (! $actor || ($actor->role ?? null) !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'action' => ['required', 'string', 'in:pro,free,delete,make_pro,make_free'],
        ]);

        $ids = array_map('intval', $validated['ids']);
        $action = (string) $validated['action'];

        // Canonicalize action names (UI may send either format).
        $action = match ($action) {
            'make_pro' => 'pro',
            'make_free' => 'free',
            default => $action,
        };

        // Safety: never allow bulk actions against the current user or any admin users.
        $query = User::query()
            ->whereIn('id', $ids)
            ->where('id', '!=', (int) $actor->id)
            ->where(function ($q) {
                $q->whereNull('role')->orWhere('role', '!=', 'admin');
            });

        $affected = 0;

        if ($action === 'pro') {
            $affected = $query->update(['plan' => 'pro']);
        } elseif ($action === 'free') {
            $affected = $query->update(['plan' => 'free']);
        } elseif ($action === 'delete') {
            $affected = $query->delete();
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'affected' => (int) $affected,
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use App\Models\Invoice;

class ClientDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // =========================
        // Dashboard Stats (Cached)
        // =========================
        $stats = Cache::remember(
            'dashboard.stats.user.' . $user->id,
            now()->addMinutes(10),
            function () use ($user) {
                return [
                    'total_invoices' => Invoice::where('user_id', $user->id)->count(),

                    'paid_amount' => Invoice::where('user_id', $user->id)
                        ->where('status', 'paid')
                        ->sum('total'),

                    'pending_amount' => Invoice::where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->sum('total'),

                    'overdue_count' => Invoice::where('user_id', $user->id)
                        ->where('status', 'overdue')
                        ->count(),
                ];
            }
        );

        // =========================
        // Recent 5 Invoices
        // =========================
        $recentInvoices = Invoice::with('client')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('client.dashboard', [
            'stats' => $stats,
            'recentInvoices' => $recentInvoices,
        ]);
    }
}

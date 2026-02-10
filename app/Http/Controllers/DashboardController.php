<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        if (! $userId) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | NEW: Date range filter (SAFE - read only)
        |--------------------------------------------------------------------------
        */
        $range = request('range', 'all'); // NEW
        $fromDate = $range !== 'all' ? now()->subDays((int)$range) : null; // NEW

        $base = Invoice::query()->where('user_id', $userId);

        // NEW: apply filter safely
        if ($fromDate) { // NEW
            $base->whereDate('created_at', '>=', $fromDate); // NEW
        }

        // Backward-compatible mapping:
        // - legacy "pending" behaves like "draft"
        // - legacy "unpaid" behaves like "sent"
        $draftStatuses = [Invoice::STATUS_DRAFT, 'pending'];
        $sentStatuses = [Invoice::STATUS_SENT, 'unpaid'];
        $paidStatuses = [Invoice::STATUS_PAID];

        $stats = [
            'total_invoices' => (clone $base)->count(),

            'draft_count' => (clone $base)->whereIn('status', $draftStatuses)->count(),
            'sent_count' => (clone $base)->whereIn('status', $sentStatuses)->count(),
            'paid_count' => (clone $base)->whereIn('status', $paidStatuses)->count(),

            'paid_amount' => (clone $base)->whereIn('status', $paidStatuses)->sum('total'),

            'pending_amount' => (clone $base)
                ->whereIn('status', array_merge($draftStatuses, $sentStatuses))
                ->sum('total'),

            'revenue_month' => (clone $base)
                ->whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->sum('total'),

            'overdue_amount' => (clone $base)->where('status', 'overdue')->sum('total'),
            'overdue_count' => (clone $base)->where('status', 'overdue')->count(),

            'avg_invoice_value' => (clone $base)->avg('total'),
            'total_clients' => Client::query()->where('user_id', $userId)->count(),
        ];

        $paidAmount = (float) ($stats['paid_amount'] ?? 0);
        $pendingAmount = (float) ($stats['pending_amount'] ?? 0);
        $stats['collection_rate'] = ($paidAmount + $pendingAmount) > 0
            ? round(($paidAmount / ($paidAmount + $pendingAmount)) * 100, 1)
            : 0;

        $monthly = (clone $base)
            ->whereNotNull('invoice_date')
            ->selectRaw("DATE_FORMAT(invoice_date, '%Y-%m') as ym, SUM(total) as total")
            ->groupBy('ym')
            ->orderBy('ym', 'desc')
            ->limit(6)
            ->get()
            ->reverse()
            ->values();

        $months = $monthly
            ->map(fn ($row) => \Carbon\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y'))
            ->values();

        $revenues = $monthly
            ->map(fn ($row) => (float) $row->total)
            ->values();

        $monthMap = $monthly->pluck('total', 'ym')->all();
        $chartLabels = collect(range(5, 0))
            ->map(function ($i) {
                return now()->subMonths($i);
            })
            ->values();

        $chartData = $chartLabels
            ->map(function ($date) use ($monthMap) {
                $key = $date->format('Y-m');
                return (float) ($monthMap[$key] ?? 0);
            })
            ->values();

        $chartLabels = $chartLabels
            ->map(fn ($date) => $date->format('M'))
            ->values();

        $topCustomers = (clone $base)
            ->whereIn('status', $paidStatuses)
            ->selectRaw('client_id, SUM(total) as revenue')
            ->groupBy('client_id')
            ->orderByDesc('revenue')
            ->limit(5)
            ->with('client:id,name')
            ->get();

        $overdueInvoices = (clone $base)
            ->where('status', 'overdue')
            ->whereNotNull('due_date')
            ->with('client:id,name')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $search = request('search');

        $recentInvoices = (clone $base)
            ->with('client:id,name')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('invoice_number', 'like', "%$search%")
                       ->orWhereHas('client', function ($c) use ($search) {
                           $c->where('name', 'like', "%$search%");
                       });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard', [
            'stats' => $stats,
            'months' => $months,
            'revenues' => $revenues,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'topCustomers' => $topCustomers,
            'overdueInvoices' => $overdueInvoices,
            'recentInvoices' => $recentInvoices,
            'currentRange' => $range, // NEW
        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT METHODS (UNCHANGED – SAFE)
    |--------------------------------------------------------------------------
    */

    public function exportAllInvoices()
    {
        $userId = Auth::id();
        if (! $userId) {
            abort(403);
        }

        $invoices = Invoice::query()
            ->with('client')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $filename = 'invoices_all_' . now()->format('Y-m-d_H-i') . '.csv';

        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Invoice No',
                'Client',
                'Total',
                'Status',
                'Invoice Date',
                'Paid Date',
            ]);

            foreach ($invoices as $invoice) {
                fputcsv($handle, [
                    $invoice->invoice_number,
                    $invoice->client->name ?? '-',
                    $invoice->total,
                    $invoice->status,
                    optional($invoice->invoice_date)->format('d-m-Y'),
                    optional($invoice->paid_at)->format('d-m-Y'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportMonthInvoices()
    {
        $userId = Auth::id();
        if (! $userId) {
            abort(403);
        }

        $invoices = Invoice::query()
            ->with('client')
            ->where('user_id', $userId)
            ->whereMonth('invoice_date', now()->month)
            ->whereYear('invoice_date', now()->year)
            ->latest()
            ->get();

        $filename = 'invoices_month_' . now()->format('Y-m') . '.csv';

        return response()->streamDownload(function () use ($invoices) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Invoice No',
                'Client',
                'Total',
                'Status',
                'Invoice Date',
                'Paid Date',
            ]);

            foreach ($invoices as $invoice) {
                fputcsv($handle, [
                    $invoice->invoice_number,
                    $invoice->client->name ?? '-',
                    $invoice->total,
                    $invoice->status,
                    optional($invoice->invoice_date)->format('d-m-Y'),
                    optional($invoice->paid_at)->format('d-m-Y'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function searchInvoices(Request $request)
    {
        $userId = Auth::id();

        $q = $request->q;

        $invoices = Invoice::query()
            ->with('client:id,name')
            ->where('user_id', $userId)
            ->where(function ($query) use ($q) {
                $query->where('invoice_number', 'like', "%$q%")
                      ->orWhere('status', 'like', "%$q%")
                      ->orWhereHas('client', fn ($c) =>
                            $c->where('name', 'like', "%$q%")
                      );
            })
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($invoices);
    }
}

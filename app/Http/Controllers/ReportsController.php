<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        if (! $userId) {
            abort(403);
        }

        $base = Invoice::query()->where('user_id', $userId);

        $amountExpr = DB::raw('COALESCE(grand_total, total)');
        $gstExpr = DB::raw('COALESCE(cgst_total, 0) + COALESCE(sgst_total, 0) + COALESCE(igst_total, 0)');

        $draftStatuses = [Invoice::STATUS_DRAFT, 'pending'];
        $sentStatuses = [Invoice::STATUS_SENT, 'unpaid'];
        $paidStatuses = [Invoice::STATUS_PAID];

        $summary = [
            'total_invoices' => (clone $base)->count(),
            'gst_collected' => (clone $base)->sum($gstExpr),
            'total_revenue' => (clone $base)->sum($amountExpr),
            'subtotal_sum' => (clone $base)->sum(
                DB::raw('COALESCE(grand_total, total) - (COALESCE(cgst_total, 0) + COALESCE(sgst_total, 0) + COALESCE(igst_total, 0))')
            ),
            'paid_count' => (clone $base)->whereIn('status', $paidStatuses)->count(),
            'pending_count' => (clone $base)
                ->whereIn('status', array_merge($draftStatuses, $sentStatuses))
                ->count(),
        ];

        $gstTrend = (clone $base)
            ->where('created_at', '>=', now()->subMonths(6))
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as ym,
                SUM(COALESCE(cgst_total,0) + COALESCE(sgst_total,0) + COALESCE(igst_total,0)) as gst
            ")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $gstTrendLabels = $gstTrend
            ->map(fn ($row) => \Carbon\Carbon::createFromFormat('Y-m', $row->ym)->format('M Y'))
            ->values();

        $gstTrendData = $gstTrend
            ->map(fn ($row) => (float) $row->gst)
            ->values();

        return view('reports.index', [
            'summary' => $summary,
            'gstTrendLabels' => $gstTrendLabels,
            'gstTrendData' => $gstTrendData,
        ]);
    }

    public function downloadPdf()
    {
        $userId = Auth::id();

        if (! $userId) {
            abort(403);
        }

        $base = Invoice::query()->where('user_id', $userId);

        $amountExpr = DB::raw('COALESCE(grand_total, total)');
        $gstExpr = DB::raw('COALESCE(cgst_total, 0) + COALESCE(sgst_total, 0) + COALESCE(igst_total, 0)');

        $draftStatuses = [Invoice::STATUS_DRAFT, 'pending'];
        $sentStatuses = [Invoice::STATUS_SENT, 'unpaid'];
        $paidStatuses = [Invoice::STATUS_PAID];

        $summary = [
            'total_invoices' => (clone $base)->count(),
            'gst_collected' => (clone $base)->sum($gstExpr),
            'total_revenue' => (clone $base)->sum($amountExpr),
            'subtotal_sum' => (clone $base)->sum(
                DB::raw('COALESCE(grand_total, total) - (COALESCE(cgst_total, 0) + COALESCE(sgst_total, 0) + COALESCE(igst_total, 0))')
            ),
            'paid_count' => (clone $base)->whereIn('status', $paidStatuses)->count(),
            'pending_count' => (clone $base)
                ->whereIn('status', array_merge($draftStatuses, $sentStatuses))
                ->count(),
        ];

        $pdf = Pdf::loadView('reports.pdf', [
            'summary' => $summary,
        ]);

        return $pdf->download('reports-summary.pdf');
    }
}


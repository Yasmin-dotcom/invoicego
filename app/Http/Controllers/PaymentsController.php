<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $userId = (int) optional($user)->id;

        $payments = Invoice::query()
            ->with('client')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereHas('client', fn ($c) => $c->where('user_id', $userId));
            })
            ->where('status', 'paid')
            ->latest('paid_at')
            ->latest()
            ->get()
            ->map(function (Invoice $inv) {
                // Prefer existing GST totals if available
                $gstFromTotals = (float) (($inv->cgst_total ?? 0) + ($inv->sgst_total ?? 0) + ($inv->igst_total ?? 0));

                if ($gstFromTotals > 0) {
                    // When GST totals are present, treat invoice total as subtotal and derive GST
                    $subtotal = (float) ($inv->total ?? 0);
                    $gst = $gstFromTotals;
                } else {
                    // Fallback: simple split assuming total includes GST at some rate
                    $subtotal = (float) ($inv->total ?? 0);
                    $gst = 0.0;
                }

                $inv->subtotal_calc = round($subtotal, 2);
                $inv->gst_calc = round($gst, 2);

                return $inv;
            });

        return view('payments.index', compact('payments'));
    }
}


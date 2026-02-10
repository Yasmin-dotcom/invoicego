<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Client invoices list (Phase 2).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $invoices = Invoice::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get(['id', 'invoice_number', 'total', 'status', 'razorpay_order_id', 'created_at']);

        return view('client.invoices.index', compact('user', 'invoices'));
    }

    /**
     * Show create invoice form.
     */
    public function create(): View
    {
        $user = Auth::user();

        return view('client.invoices.create', compact('user'));
    }

    /**
     * Store a new invoice (server-side free vs pro enforcement).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (($user->plan ?? 'free') === 'free') {
            $invoiceCount = Invoice::where('user_id', $user->id)->count();
            if ($invoiceCount >= 20) {
                return redirect('/upgrade')
                    ->with('error', 'Invoice limit reached. Upgrade to Pro.');
            }
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        // (V1) Free vs Pro enforcement handled above using users.plan

        // Client invoice must be linked to the authenticated user's client record
        $client = Client::firstOrCreate(
            ['user_id' => $user->id],
            ['name' => $user->name, 'email' => $user->email],
        );

        DB::transaction(function () use ($user, $client, $validated) {
            $sequence = Invoice::where('user_id', $user->id)->count() + 1;
            $invoiceNumber = 'INV-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            Invoice::create([
                'user_id' => $user->id,
                'client_id' => $client->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => Carbon::today(),
                'total' => $validated['amount'],
                'status' => Invoice::STATUS_DRAFT,
            ]);
        });

        return redirect()
            ->route('client.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }
}


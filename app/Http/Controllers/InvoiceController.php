<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Mail\InvoiceCreatedMail;
use App\Mail\InvoicePaidMail;
use App\Mail\InvoicePaymentReminderMail;
use App\Services\RazorpayService;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Scope all invoice reads/writes to the authenticated user (legacy-safe via client.user_id).
        $userScoped = Invoice::query()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('client', fn ($c) => $c->where('user_id', $user->id));
            });

        // Never allow one user to auto-mark other users' invoices.
        (clone $userScoped)->autoMarkOverdue();

        $status = strtolower(trim((string) $request->query('status', '')));
        $allowed = [Invoice::STATUS_DRAFT, Invoice::STATUS_SENT, Invoice::STATUS_PAID];

        $query = (clone $userScoped)
            ->with(['client', 'items'])
            ->latest();

        if (in_array($status, $allowed, true)) {
            // Backward-compatible mapping for legacy stored statuses.
            if ($status === Invoice::STATUS_DRAFT) {
                $query->where(function ($q) {
                    $q->whereIn('status', [Invoice::STATUS_DRAFT, 'pending', ''])
                        ->orWhereNull('status');
                });
            } else {
                $query->whereIn('status', match ($status) {
                    Invoice::STATUS_SENT => [Invoice::STATUS_SENT, 'unpaid'],
                    Invoice::STATUS_PAID => [Invoice::STATUS_PAID],
                });
            }
        }

        $invoices = $query->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(): View
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Pro users: skip invoice limit checks entirely.
        if (auth()->user()->isPlanPro()) {
            // no-op
        } else {
        // V1: Free vs Pro invoice limit (read-only check).
        // If user has no plan column/value, safely treat as "free".
        $plan = strtolower(trim((string) ($user->plan ?? 'free')));
        $isPro = $plan === 'pro';

        if (! $isPro) {
            $limit = config('plans.free.invoice_limit', 5);

            if ($limit !== null) {
                $invoiceCount = Invoice::query()
                    ->where(function ($q) use ($user) {
                        $q->where('user_id', $user->id)
                            ->orWhereHas('client', fn ($c) => $c->where('user_id', $user->id));
                    })
                    ->count();

                if ($invoiceCount >= (int) $limit) {
                    return redirect()
                        ->route('invoices.index')
                        ->with('error', 'Free plan invoice limit reached. Upgrade to Pro.');
                }
            }
        }
        }

        // V1: only show the authenticated user's clients.
        $clients = Client::query()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('invoices.create', compact('clients'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // V1 minimal draft create flow: if no items are present, create a draft invoice.
        // This path intentionally avoids payments/emails and keeps it suitable for V1 testing.
        if (! $request->filled('items')) {
            $clientExistsRule = Rule::exists('clients', 'id');
            if (! $user->isAdmin()) {
                $clientExistsRule = $clientExistsRule->where(fn ($q) => $q->where('user_id', $user->id));
            }

            $validated = $request->validate([
                'client_id' => [
                    'required',
                    $clientExistsRule,
                ],
                'due_date' => ['nullable', 'date'],
            ]);

            $today = Carbon::today();
            $dueDate = isset($validated['due_date']) && $validated['due_date']
                ? Carbon::parse($validated['due_date'])
                : $today->copy()->addDays(7);

            $sequence = Invoice::where('user_id', $user->id)->count() + 1;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $validated['client_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $today,
                'due_date' => $dueDate,
                'total' => 0,
                'status' => Invoice::STATUS_DRAFT,
            ]);

            return redirect()
                ->route('invoices.show', $invoice)
                ->with('success', 'Draft invoice created successfully.');
        }

        /**
         * 🔒 V2.3 — Invoice limit messaging (Free users only)
         */
        if (! $user->isPro()) {
            $invoiceCount = Invoice::whereHas('client', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

            if ($invoiceCount >= 20) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Free plan allows up to 20 invoices. Upgrade to Pro to create unlimited invoices.');
            }
        }

        $validated = $request->validate([
            'client_id' => [
                'required',
                Rule::exists('clients', 'id')->where(function ($q) use ($user) {
                    // Admin can reference any client; normal users are scoped to their own clients.
                    if ($user->isAdmin()) {
                        return;
                    }
                    $q->where('user_id', $user->id);
                }),
            ],
            'invoice_date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $total = collect($validated['items'])->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });

        $invoiceDate = Carbon::parse($validated['invoice_date']);
        $dueDate = $invoiceDate->copy()->addDays(7);

        $invoice = DB::transaction(function () use ($validated, $total, $invoiceDate, $dueDate, $user) {
            $year = date('Y');
            $sequence = Invoice::where('user_id', $user->id)->whereYear('created_at', $year)->count() + 1;
            $invoiceNumber = 'INV-' . $year . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'client_id' => $validated['client_id'],
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'total' => $total,
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            return $invoice;
        });

        try {
            $razorpayService = new RazorpayService();
            $razorpayService->createOrderForInvoice($invoice);
            $paymentLink = $razorpayService->generatePaymentLink($invoice);
            $invoice->update(['razorpay_payment_link' => $paymentLink]);
            $invoice->refresh();
        } catch (\Throwable $e) {
            try {
                $invoice->items()->delete();
                $invoice->delete();
            } catch (\Throwable $cleanup) {
                Log::error('Failed to cleanup invoice after Razorpay failure', [
                    'invoice_id' => $invoice->id,
                    'cleanup_error' => $cleanup->getMessage(),
                ]);
            }
            throw $e;
        }

        $invoice->load('client');
        if ($invoice->client && $invoice->client->email) {
            try {
                Mail::to($invoice->client->email)->send(new InvoiceCreatedMail($invoice));
            } catch (\Exception $e) {
                Log::error('Failed to send invoice created email', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadPdf(Invoice $invoice)
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }
        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        if ($invoice->markOverdueIfNeeded()) {
            $invoice->refresh();
        }

        $invoice->load(['client', 'items']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Secure download endpoint (PDF when available, HTML fallback).
     */
    public function download(Invoice $invoice)
    {
        $userId = auth()->id();
        if (! $userId) {
            abort(403);
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $userId;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $userId;
        }
        if (! $ownsInvoice) {
            abort(403);
        }

        $invoice->load(['client', 'items']);

        // Prefer PDF if the view + PDF facade work; otherwise fall back to HTML attachment.
        try {
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
            $bytes = $pdf->output();

            $tmpPath = tempnam(sys_get_temp_dir(), 'invoice_');
            if ($tmpPath === false) {
                throw new \RuntimeException('Unable to create temporary file.');
            }
            $pdfPath = $tmpPath . '.pdf';
            @rename($tmpPath, $pdfPath);
            file_put_contents($pdfPath, $bytes);

            $fileName = 'invoice-' . ($invoice->invoice_number ?: $invoice->id) . '.pdf';

            return response()
                ->download($pdfPath, $fileName, ['Content-Type' => 'application/pdf'])
                ->deleteFileAfterSend(true);
        } catch (\Throwable $e) {
            $html = view('invoices.pdf', compact('invoice'))->render();
            $fileName = 'invoice-' . ($invoice->invoice_number ?: $invoice->id) . '.html';

            return response()->make($html, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        }
    }

    /**
     * Display the specified invoice (V1).
     * Also persists overdue state when applicable.
     */
    public function show(Invoice $invoice): View
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }
        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        if ($invoice->markOverdueIfNeeded()) {
            $invoice->refresh();
        }

        $invoice->load(['client', 'items']);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Delete the specified invoice safely.
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }
        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()
            ->back()
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Mark invoice as sent (V1 status flow).
     */
    public function markAsSent(Invoice $invoice): RedirectResponse
{
    $user = Auth::user();
    if (! $user) {
        return redirect()->route('login');
    }

    // Ownership check
    $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
    if (! $ownsInvoice) {
        $invoice->loadMissing('client:id,user_id');
        $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
    }
    if (! $user->isAdmin() && ! $ownsInvoice) {
        abort(403);
    }

    // ✅ allow resend (draft OR sent OR overdue)
    $invoice->update([
        'status' => Invoice::STATUS_SENT,
    ]);

    // ✅ SEND EMAIL HERE (THIS WAS MISSING)
    $invoice->load('client');

    if ($invoice->client && $invoice->client->email) {
        try {
            Mail::to($invoice->client->email)
                ->send(new InvoiceCreatedMail($invoice));

            Log::info('Invoice sent email delivered', [
                'invoice_id' => $invoice->id,
                'email' => $invoice->client->email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send invoice sent email', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    return redirect()
        ->back()
        ->with('success', 'Invoice sent + email delivered successfully.');
}


    /**
     * Mark invoice as paid.
     */
    public function markAsPaid(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }
        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        $status = $invoice->normalizedStatus();
        if ($status === Invoice::STATUS_PAID) {
            return redirect()
                ->route('invoices.index')
                ->with('info', 'Invoice is already marked as paid.');
        }

        // V1 flow: only SENT or OVERDUE can transition to PAID.
        if (! in_array($status, [Invoice::STATUS_SENT, Invoice::STATUS_OVERDUE], true)) {
            return redirect()
                ->route('invoices.index')
                ->with('info', 'Only sent or overdue invoices can be marked as paid.');
        }

        if (strtolower((string) $invoice->status) !== 'paid') {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $invoice->load('client');
            if ($invoice->client && $invoice->client->email) {
                try {
                    Mail::to($invoice->client->email)->send(new InvoicePaidMail($invoice));
                } catch (\Exception $e) {
                    Log::error('Failed to send payment receipt email', [
                        'invoice_id' => $invoice->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice marked as paid successfully.');
    }

    /**
     * Mark invoice as paid (V1 minimal backend flow).
     * Allowed transition: SENT -> PAID only.
     */
    public function markPaid(Invoice $invoice): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // ownership check
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;

        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }

        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        // block only already paid
        if ($invoice->normalizedStatus() === Invoice::STATUS_PAID) {
            return redirect()
                ->back()
                ->with('info', 'Invoice already marked as paid.');
        }

        // allow draft/sent/overdue
        $invoice->update([
            'status' => Invoice::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Invoice marked as paid successfully.');
    }

    /**
     * Send payment reminder email for unpaid invoice.
     */
    public function sendPaymentReminder(Invoice $invoice): RedirectResponse
    {
        $invoice->load('client');

        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        // Ownership: prefer invoices.user_id, fall back to client.user_id (legacy compatibility).
        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }
        if (! $user->isAdmin() && ! $ownsInvoice) {
            abort(403);
        }

        if ($user && ! $user->isAdmin() && ! $user->isPro()) {
            $limit = (int) config('plans.free.monthly_reminders', 5);

            if ($limit >= 0) {
                $clientIds = $user->clients()->pluck('id');
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();

                $sentCountThisMonth = ReminderLog::query()
                    ->whereIn('client_id', $clientIds)
                    ->where('status', 'sent')
                    ->whereNotNull('sent_at')
                    ->whereBetween('sent_at', [$start, $end])
                    ->count();

                if ($sentCountThisMonth >= $limit) {
                    if ($invoice->client_id) {
                        ReminderLog::logOutcome($invoice, 'blocked_free_limit', 'email', 'free_limit', 'manual');
                    }

                    return redirect()
                        ->route('invoices.index')
                        ->with('error', 'Free plan reminder limit reached. Upgrade to Pro.');
                }
            }
        }

        if (! $invoice->isRemindable()) {
            return redirect()
                ->route('invoices.index')
                ->with('info', 'Payment reminder can only be sent for sent or overdue invoices.');
        }

        if (! $invoice->client || ! $invoice->client->email) {
            return redirect()
                ->route('invoices.index')
                ->with('error', 'Client email not found. Cannot send payment reminder.');
        }

        try {
            Mail::to($invoice->client->email)->send(new InvoicePaymentReminderMail($invoice));
            ReminderLog::logOutcome($invoice, 'sent', 'email', null, 'manual');

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Payment reminder sent successfully.');
        } catch (\Exception $e) {
            ReminderLog::logOutcome($invoice, 'failed', 'email', 'exception', 'manual', $e->getMessage());

            return redirect()
                ->route('invoices.index')
                ->with('error', 'Failed to send payment reminder. Please try again.');
        }
    }

      /*
|--------------------------------------------------------------------------
| Live Search (AJAX)
|--------------------------------------------------------------------------
*/
public function search(\Illuminate\Http\Request $request)
{
    $query = $request->get('search');

    $invoices = \App\Models\Invoice::with('client')
        ->when($query, function ($q) use ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('invoice_number', 'like', "%{$query}%")
                    ->orWhereHas('client', function ($clientQuery) use ($query) {
                        $clientQuery->where('name', 'like', "%{$query}%")
                                    ->orWhere('email', 'like', "%{$query}%");
                    });
            });
        })
        ->latest()
        ->get();

    if ($request->ajax()) {
        return view('invoices.partials.table', compact('invoices'))->render();
    }

    return redirect()->route('invoices.index');
}
/**
 * Show edit invoice form (V2 safe feature)
 */
public function edit(Invoice $invoice): View
{
    $user = Auth::user();
    if (! $user) {
        abort(403);
    }

    // ownership check (same pattern as show())
    $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
    if (! $ownsInvoice) {
        $invoice->loadMissing('client:id,user_id');
        $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
    }
    if (! $user->isAdmin() && ! $ownsInvoice) {
        abort(403);
    }

    // 🚫 don't allow editing paid invoices
    if ($invoice->normalizedStatus() === Invoice::STATUS_PAID) {
        return redirect()
            ->route('invoices.index')
            ->with('error', 'Paid invoices cannot be edited.');
    }

    $invoice->load(['client', 'items']);

    $clients = Client::where('user_id', $user->id)
        ->orderBy('name')
        ->get();

    return view('invoices.edit', compact('invoice', 'clients'));
}


/**
 * Update invoice safely (NO Razorpay touch)
 */
public function update(Request $request, Invoice $invoice): RedirectResponse
{
    $user = Auth::user();
    if (! $user) {
        abort(403);
    }

    // ownership check
    $ownsInvoice = (int) $invoice->user_id === (int) $user->id;
    if (! $ownsInvoice) {
        $invoice->loadMissing('client:id,user_id');
        $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
    }
    if (! $user->isAdmin() && ! $ownsInvoice) {
        abort(403);
    }

    // 🚫 paid invoice locked
    if ($invoice->normalizedStatus() === Invoice::STATUS_PAID) {
        return redirect()->back()->with('error', 'Paid invoices cannot be edited.');
    }

    $validated = $request->validate([
        'client_id' => ['required', 'exists:clients,id'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.description' => ['required', 'string'],
        'items.*.quantity' => ['required', 'numeric', 'min:1'],
        'items.*.price' => ['required', 'numeric', 'min:0'],
    ]);

    DB::transaction(function () use ($invoice, $validated) {

        $invoice->update([
            'client_id' => $validated['client_id'],
        ]);

        // delete old items
        $invoice->items()->delete();

        $total = 0;

        foreach ($validated['items'] as $item) {
            $total += $item['quantity'] * $item['price'];

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $invoice->update([
            'total' => $total,
        ]);
    });

    return redirect()
        ->route('invoices.show', $invoice)
        ->with('success', 'Invoice updated successfully.');
}

    public function bulkSend(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $ids = $validated['ids'];

        $query = Invoice::query()->whereIn('id', $ids);
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('client', function ($clientQuery) use ($user) {
                        $clientQuery->where('user_id', $user->id);
                    });
            });
        }

        $invoices = $query->get();

        if (! $user->isAdmin() && $invoices->count() !== count($ids)) {
            abort(403);
        }

        Invoice::query()
            ->whereIn('id', $invoices->pluck('id'))
            ->update(['status' => Invoice::STATUS_SENT]);

        return redirect()
            ->back()
            ->with('success', 'Bulk action completed.');
    }

    public function bulkMarkPaid(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $ids = $validated['ids'];

        $query = Invoice::query()->whereIn('id', $ids);
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('client', function ($clientQuery) use ($user) {
                        $clientQuery->where('user_id', $user->id);
                    });
            });
        }

        $invoices = $query->get();

        if (! $user->isAdmin() && $invoices->count() !== count($ids)) {
            abort(403);
        }

        Invoice::query()
            ->whereIn('id', $invoices->pluck('id'))
            ->update([
                'status' => Invoice::STATUS_PAID,
                'paid_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with('success', 'Bulk action completed.');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $ids = $validated['ids'];

        $query = Invoice::query()->whereIn('id', $ids);
        if (! $user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('client', function ($clientQuery) use ($user) {
                        $clientQuery->where('user_id', $user->id);
                    });
            });
        }

        $invoices = $query->get();

        if (! $user->isAdmin() && $invoices->count() !== count($ids)) {
            abort(403);
        }

        DB::transaction(function () use ($invoices) {
            foreach ($invoices as $invoice) {
                $invoice->items()->delete();
                $invoice->delete();
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Bulk action completed.');
    }

    public function exportCsv()
    {
        $type = request('type');          // page | all
        $ids  = request('ids', []);       // selected ids
    
        $user = auth()->user();
    
        /*
        |--------------------------------------------------------------------------
        | Base Query (single source of truth)
        |--------------------------------------------------------------------------
        */
        $query = Invoice::with('client')
            ->where('user_id', $user->id)
            ->latest();
    
    
        /*
        |--------------------------------------------------------------------------
        | Date Range Filter (optional)
        |--------------------------------------------------------------------------
        */
        $from = request('from');
        $to   = request('to');
    
        if ($from && $to) {
            $query->whereBetween('invoice_date', [$from, $to]);
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Selected IDs Filter (optional)
        |--------------------------------------------------------------------------
        */
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | Fetch invoices
        | Priority: selected > page > all
        |--------------------------------------------------------------------------
        */
        if (!empty($ids)) {
            $invoices = $query->get();
        } elseif ($type === 'page') {
            $invoices = $query->paginate(10)->getCollection();
        } else {
            $invoices = $query->get();
        }
    
    
        /*
        |--------------------------------------------------------------------------
        | CSV Export
        |--------------------------------------------------------------------------
        */
        $filename = 'invoices_' . now()->format('Y-m-d_H-i') . '.csv';
    
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
    
        $callback = function () use ($invoices) {
    
            $handle = fopen('php://output', 'w');
    
            // Header row
            fputcsv($handle, [
                'Invoice No',
                'Client',
                'Total',
                'Status',
                'Invoice Date',
                'Paid Date',
            ]);
    
            // Data rows
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
        };
    
        return response()->stream($callback, 200, $headers);
    }    

}

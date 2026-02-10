<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceItemController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->authorizeInvoiceOwnership($invoice);

        if ($invoice->normalizedStatus() !== Invoice::STATUS_DRAFT) {
            return back()->with('error', 'Invoice items can only be edited while the invoice is in DRAFT.');
        }

        $data = $request->validate([
            // Support both "name" (V1 UI) and legacy "description" (DB column)
            'name' => ['nullable', 'string', 'max:255', 'required_without:description'],
            'description' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $description = (string) ($data['name'] ?? $data['description']);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => $description,
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);

        $this->recalculateInvoiceTotal($invoice);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Item added.');
    }

    public function update(Request $request, InvoiceItem $item): RedirectResponse
    {
        $item->loadMissing('invoice');
        $invoice = $item->invoice;

        if (! $invoice) abort(404);

        $this->authorizeInvoiceOwnership($invoice);

        if ($invoice->normalizedStatus() !== Invoice::STATUS_DRAFT) {
            return back()->with('error', 'Invoice items can only be edited while the invoice is in DRAFT.');
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255', 'required_without:description'],
            'description' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $item->update([
            'description' => (string) ($data['name'] ?? $data['description']),
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);

        $this->recalculateInvoiceTotal($invoice);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Item updated.');
    }

    public function destroy(InvoiceItem $item): RedirectResponse
    {
        $item->loadMissing('invoice');
        $invoice = $item->invoice;

        if (! $invoice) abort(404);

        $this->authorizeInvoiceOwnership($invoice);

        if ($invoice->normalizedStatus() !== Invoice::STATUS_DRAFT) {
            return back()->with('error', 'Invoice items can only be edited while the invoice is in DRAFT.');
        }

        $item->delete();

        $this->recalculateInvoiceTotal($invoice);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Item removed.');
    }

    /**
     * 🔥 CORE FIX: DB-level total sync
     */
    private function recalculateInvoiceTotal(Invoice $invoice): void
    {
        // Extra guard: never mutate totals for non-draft invoices
        if ($invoice->normalizedStatus() !== Invoice::STATUS_DRAFT) {
            return;
        }

        $total = DB::table('invoice_items')
            ->where('invoice_id', $invoice->id)
            ->selectRaw('COALESCE(SUM(quantity * price), 0) as total')
            ->value('total');

        DB::table('invoices')
            ->where('id', $invoice->id)
            ->update(['total' => round((float) $total, 2)]);
    }

    private function authorizeInvoiceOwnership(Invoice $invoice): void
    {
        $user = Auth::user();
        if (! $user) abort(403);

        $ownsInvoice = (int) $invoice->user_id === (int) $user->id;

        if (! $ownsInvoice) {
            $invoice->loadMissing('client:id,user_id');
            $ownsInvoice = $invoice->client && (int) $invoice->client->user_id === (int) $user->id;
        }

        if (! $ownsInvoice) abort(403);
    }
}

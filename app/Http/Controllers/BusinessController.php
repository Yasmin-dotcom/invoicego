<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('settings.business', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'gstin' => ['nullable', 'string', 'max:20'],
            'state_code' => ['nullable', 'string', 'max:10'],
            'business_address' => ['nullable', 'string', 'max:2000'],
            'business_city' => ['nullable', 'string', 'max:100'],
            'business_state' => ['nullable', 'string', 'max:100'],
            'business_pincode' => ['nullable', 'string', 'max:10'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_ifsc' => ['nullable', 'string', 'max:20'],
            'invoice_prefix' => ['nullable', 'string', 'max:50'],
        ]);

        $user->forceFill([
            'business_name' => $validated['business_name'] ?? $user->business_name,
            'gstin' => $validated['gstin'] ?? $user->gstin,
            'state_code' => $validated['state_code'] ?? $user->state_code,
            'business_address' => $validated['business_address'] ?? $user->business_address,
            'business_city' => $validated['business_city'] ?? $user->business_city,
            'business_state' => $validated['business_state'] ?? $user->business_state,
            'business_pincode' => $validated['business_pincode'] ?? $user->business_pincode,
            'bank_name' => $validated['bank_name'] ?? $user->bank_name,
            'bank_branch' => $validated['bank_branch'] ?? $user->bank_branch,
            'bank_account_name' => $validated['bank_account_name'] ?? $user->bank_account_name,
            'bank_account_number' => $validated['bank_account_number'] ?? $user->bank_account_number,
            'bank_ifsc' => $validated['bank_ifsc'] ?? $user->bank_ifsc,
            'invoice_prefix' => $validated['invoice_prefix'] ?? $user->invoice_prefix,
        ])->save();

        return redirect()
            ->route('settings.business')
            ->with('success', 'Business information updated.');
    }
}


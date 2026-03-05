<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->onboarded) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.business');
    }

    public function storeBusiness(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'max:10'],
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
            'invoice_prefix' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = Storage::disk('public')->putFile('logos', $request->file('logo'));
        }

        $user->forceFill([
            'business_name' => $validated['business_name'],
            'currency' => $validated['currency'],
            'gstin' => $validated['gstin'] ?? null,
            'state_code' => $validated['state_code'] ?? null,
            'business_address' => $validated['business_address'] ?? null,
            'business_city' => $validated['business_city'] ?? null,
            'business_state' => $validated['business_state'] ?? null,
            'business_pincode' => $validated['business_pincode'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_branch' => $validated['bank_branch'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_ifsc' => $validated['bank_ifsc'] ?? null,
            'invoice_prefix' => $validated['invoice_prefix'] ?? null,
            'logo_path' => $logoPath ?? $user->logo_path,
        ])->save();

        return redirect()->route('onboarding.client');
    }

    public function client(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user && $user->onboarded) {
            return redirect()->route('dashboard');
        }

        return view('onboarding.client');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_email' => ['nullable', 'email', 'max:255'],
        ]);

        if (! empty($validated['client_name']) || ! empty($validated['client_email'])) {
            Client::create([
                'user_id' => $user->id,
                'name' => $validated['client_name'] ?? 'First Client',
                'email' => $validated['client_email'] ?? null,
            ]);
        }

        $user->forceFill(['onboarded' => true])->save();

        return redirect()->route('dashboard');
    }

    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $user->forceFill(['onboarded' => true])->save();

        return redirect()->route('dashboard');
    }
}

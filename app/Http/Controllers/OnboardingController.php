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
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = Storage::disk('public')->putFile('logos', $request->file('logo'));
        }

        $user->forceFill([
            'business_name' => $validated['business_name'],
            'currency' => $validated['currency'],
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     */
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client.
     */
    public function create(): View
    {
        return view('clients.create');
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => ['nullable','digits_between:10,15'],
            'address' => 'nullable|string',
            'enable_reminders' => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['enable_reminders'] = $request->has('enable_reminders');

        Client::create($data);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'reminder_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['reminder_enabled'] = $request->has('reminder_enabled');

        // V2.2 — Reminder limit on update
        if (
            $user
            && $validated['reminder_enabled']
            && ! $user->isPro()
            && $user->hasReachedActiveReminderLimit(5, $client->id)
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Free plan allows only 5 active reminders. Upgrade to Pro to enable more.');
        }

        $client->update($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }
}

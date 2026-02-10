<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ReminderLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReminderLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $client = $user?->clients()->first();

        if (!$client) {
            abort(404, 'Client record not found.');
        }

        // Monetization hook placeholder: treat all users as FREE unless you add a plan field later.
        $isPro = (bool) ($user->is_pro ?? false);

        if (!$isPro) {
            return view('client.reminder-logs.index', [
                'isPro' => false,
                'logs' => null,
            ]);
        }

        $logs = ReminderLog::query()
            ->with(['invoice'])
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('client.reminder-logs.index', [
            'isPro' => true,
            'logs' => $logs,
        ]);
    }
}

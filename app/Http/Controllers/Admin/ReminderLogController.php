<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReminderLog;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReminderLog::query()
            ->with(['invoice', 'client'])
            ->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($channel = $request->string('channel')->toString()) {
            $query->where('channel', $channel);
        }

        if ($from = $request->string('from')->toString()) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->string('to')->toString()) {
            $query->whereDate('created_at', '<=', $to);
        }

        // Optional client filter (useful for admin debugging)
        if ($clientId = $request->integer('client_id')) {
            $query->where('client_id', $clientId);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Lightweight list for filter dropdown (small dataset expected)
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('admin.reminder-logs.index', [
            'logs' => $logs,
            'clients' => $clients,
            'filters' => $request->only(['status', 'channel', 'from', 'to', 'client_id']),
        ]);
    }
}

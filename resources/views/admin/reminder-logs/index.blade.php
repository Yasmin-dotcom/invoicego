@extends('layouts.dashboard')

@section('title', 'Reminder Logs')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reminder Logs</h1>
        <p class="text-sm text-gray-600 mt-1">Transparency and debugging for reminder delivery across the SaaS.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.reminder-logs') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm">
                        <option value="">All</option>
                        @foreach(['sent' => 'Sent', 'skipped' => 'Skipped', 'failed' => 'Failed'] as $k => $v)
                            <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Channel</label>
                    <select name="channel" class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm">
                        <option value="">All</option>
                        @foreach(['email' => 'Email', 'whatsapp' => 'WhatsApp', 'sms' => 'SMS'] as $k => $v)
                            <option value="{{ $k }}" @selected(($filters['channel'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">From</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">To</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Client</label>
                    <select name="client_id" class="w-full rounded-md border border-gray-300 px-2 py-2 text-sm">
                        <option value="">All clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" @selected((string)($filters['client_id'] ?? '') === (string)$c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-6 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.reminder-logs') }}" class="text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    <button type="submit" class="rounded-md bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                        Apply filters
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Reminder Type</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Channel</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Reason</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ optional($log->created_at)->format('d M Y, H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->invoice?->invoice_number ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $log->client?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $log->reminder_type ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ strtoupper($log->channel ?? '—') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $status = $log->status;
                                    $badge = match($status) {
                                        'sent' => 'bg-green-100 text-green-800',
                                        'skipped' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                    {{ strtoupper($status ?? '—') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $log->reason ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                No reminder logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
@endsection


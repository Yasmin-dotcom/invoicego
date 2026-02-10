@extends('layouts.dashboard')

@section('title', 'Reminder Logs')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reminder Logs</h1>
        <p class="text-sm text-gray-600 mt-1">See delivery history for reminders sent to your account.</p>
    </div>

    @if(!$isPro)
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="text-lg font-semibold text-gray-900">Reminder logs are available in Pro plan</div>
            <p class="text-sm text-gray-600 mt-2">
                Upgrade to Pro to access detailed reminder delivery history and analytics.
            </p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Reminder Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Channel</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
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
    @endif
@endsection


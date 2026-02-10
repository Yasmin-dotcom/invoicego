<x-app-layout>
     
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-slate-50 to-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-6 space-y-6">

            {{-- Welcome --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                <div class="text-lg text-gray-900 font-semibold">
                    Welcome back, {{ auth()->user()->name }} 👋
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Here’s a quick overview of your business.
                </div>
            </div>

            {{-- Upgrade CTA --}}
            @if (
                auth()->check() &&
                in_array(auth()->user()->role, ['owner', 'client'], true) &&
                ! auth()->user()?->isPlanPro() &&
                auth()->user()->plan === 'free'
            )
                <x-upgrade-cta />
            @endif

            {{-- Date Range Filters --}}
<div class="flex flex-wrap gap-2 mb-2">

    @php
        $ranges = [
            'all' => 'All',
            '1'   => 'Today',
            '7'   => '7 Days',
            '30'  => '30 Days',
            '90'  => '90 Days',
        ];
    @endphp

    @foreach ($ranges as $value => $label)
        <a href="{{ url('/dashboard?range='.$value) }}"
           class="px-3 py-1 text-sm rounded-md transition shadow-sm
           {{ ($currentRange ?? 'all') == $value
                ? 'bg-indigo-600 text-white'
                : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
    @endforeach

</div>

            {{-- Row 1 : Counts --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Total Invoices</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($stats['total_invoices'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Draft</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($stats['draft_count'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Sent</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($stats['sent_count'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Paid</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($stats['paid_count'] ?? 0) }}
                    </div>
                </div>

            </div>

            {{-- Row 2 : Money --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Paid Amount</div>
                    <div class="text-2xl font-bold mt-1 text-green-600">
                        ₹{{ number_format((float) ($stats['paid_amount'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Pending Amount</div>
                    <div class="text-2xl font-bold mt-1 text-yellow-600">
                        ₹{{ number_format((float) ($stats['pending_amount'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">This Month Revenue</div>
                    <div class="text-2xl font-bold mt-1 text-indigo-600">
                        ₹{{ number_format((float) ($stats['revenue_month'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Overdue Amount</div>
                    <div class="text-2xl font-bold mt-1 text-red-600">
                        ₹{{ number_format((float) ($stats['overdue_amount'] ?? 0), 2) }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        ({{ (int) ($stats['overdue_count'] ?? 0) }} invoices)
                    </div>
                </div>

            </div>

            {{-- Row 3 : Monthly Summary --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Avg Invoice Value</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        ₹{{ number_format((float) ($stats['avg_invoice_value'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Total Clients</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($stats['total_clients'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Overdue Count</div>
                    <div class="text-2xl font-bold mt-1 text-red-600">
                        {{ (int) ($stats['overdue_count'] ?? 0) }}
                    </div>
                </div>

                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Collection Rate %</div>
                    <div class="text-2xl font-bold mt-1 text-green-600">
                        {{ number_format((float) ($stats['collection_rate'] ?? 0), 1) }}%
                    </div>
                </div>
            </div>

            {{-- Revenue (Last 6 Months) --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                <div class="text-sm text-gray-600 mb-4">Revenue (Last 6 Months)</div>
                <div class="w-full">
                    <canvas id="revenueChart" height="90"></canvas>
                </div>
            </div>

            {{-- Top Customers --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition">
                {{-- IMPORTANT: p-6 (not p-8) for table --}}
                <div class="p-6">
                    <h3 class="text-md text-gray-900 font-semibold mb-4">Top Customers</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-gray-600">Client</th>
                                    <th class="text-left py-2 text-gray-600">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topCustomers ?? [] as $row)
                                    <tr class="border-b text-gray-700">
                                        <td class="py-2">{{ $row->client->name ?? '-' }}</td>
                                        <td class="py-2">₹{{ number_format((float) $row->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-gray-500">
                                            No customers found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Overdue Invoices --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition">
                {{-- IMPORTANT: p-6 (not p-8) for table --}}
                <div class="p-6">
                    <h3 class="text-md font-semibold mb-4 text-red-600">Overdue Invoices</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-gray-600">Client</th>
                                    <th class="text-left py-2 text-gray-600">Amount</th>
                                    <th class="text-left py-2 text-gray-600">Due Date</th>
                                    <th class="text-left py-2 text-gray-600">Days Late</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($overdueInvoices ?? [] as $invoice)
                                    <tr class="border-b text-gray-700">
                                        <td class="py-2">{{ $invoice->client->name ?? '-' }}</td>
                                        <td class="py-2 text-red-600">₹{{ number_format($invoice->total, 2) }}</td>
                                        <td class="py-2">
                                            {{ optional($invoice->due_date)->format('d M Y') }}
                                        </td>
                                        <td class="py-2 text-red-600">
                                @php
                                        $days = 0;
                                        $hours = 0;

                                if ($invoice->due_date) {
                                        $diff = now()->diff($invoice->due_date);
                                        $days = $diff->days;
                                        $hours = $diff->h;
                                        }
                                @endphp

                                    {{ $days }} days {{ $hours }} hours
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">
                                            No overdue invoices.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/invoices/create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm shadow-sm hover:shadow-md hover:bg-indigo-700 transition">
                    + Create Invoice
                </a>

                <a href="{{ url('/clients/create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm shadow-sm hover:shadow-md hover:bg-indigo-700 transition">
                    + Add Client
                </a>

                <a href="{{ url('/invoices') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm shadow-sm hover:shadow-md hover:bg-indigo-700 transition">
                    View All Invoices
                </a>

                <a href="{{ route('dashboard.export.all') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm shadow-sm hover:shadow-md hover:bg-indigo-700 transition">
                    Export All Invoices (CSV)
                </a>

                <a href="{{ route('dashboard.export.month') }}"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm shadow-sm hover:shadow-md hover:bg-indigo-700 transition">
                    Export This Month (CSV)
                </a>
            </div>

            {{-- Recent Invoices --}}
            <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition">
                {{-- IMPORTANT: p-6 (not p-8) for table --}}
                <div class="p-6">
                    <h3 class="text-md text-gray-900 font-semibold mb-4">Recent Invoices</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 text-gray-600">Invoice #</th>
                                    <th class="text-left py-2 text-gray-600">Client</th>
                                    <th class="text-left py-2 text-gray-600">Amount</th>
                                    <th class="text-left py-2 text-gray-600">Status</th>
                                    <th class="text-left py-2 text-gray-600">Date</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($recentInvoices ?? [] as $invoice)
                                    <tr class="border-b hover:bg-gray-50 cursor-pointer text-gray-700"
                                        onclick="window.location='{{ url('/invoices/'.$invoice->id) }}'">
                                        <td class="py-2">{{ $invoice->invoice_number }}</td>
                                        <td class="py-2">{{ $invoice->client->name ?? '-' }}</td>
                                        <td class="py-2">₹{{ number_format($invoice->total, 2) }}</td>
                                        <td class="py-2 capitalize">{{ $invoice->status }}</td>
                                        <td class="py-2">{{ $invoice->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                    <td colspan="5" class="text-center text-gray-500">
                                     No invoices found.
                                    </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($recentInvoices) && method_exists($recentInvoices, 'links'))
    <div class="mt-4">
        {{ $recentInvoices->links() }}
    </div>
@endif

                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const canvas = document.getElementById('revenueChart');
            if (!canvas) return;

            const labels = @json($chartLabels ?? $months ?? []);
            const data = @json($chartData ?? $revenues ?? []);
            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Revenue',
                        data,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#4F46E5',
                        borderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `₹${Number(ctx.parsed.y || 0).toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                autoSkip: false
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.2)'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => `₹${Number(value).toLocaleString()}`
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.2)'
                            }
                        }
                    }
                }
            });
        })();
    </script>

</x-app-layout>

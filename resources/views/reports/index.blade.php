<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reports') }}
            </h2>
            <a href="{{ route('reports.download.pdf') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 shadow-sm">
                Download PDF
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Revenue --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Total Revenue</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        ₹{{ number_format((float) ($summary['total_revenue'] ?? 0), 2) }}
                    </div>
                </div>

                {{-- GST Collected --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">GST Collected</div>
                    <div class="text-2xl font-bold mt-1 text-emerald-600">
                        ₹{{ number_format((float) ($summary['gst_collected'] ?? 0), 2) }}
                    </div>
                </div>

                {{-- Total Invoices --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Total Invoices</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        {{ (int) ($summary['total_invoices'] ?? 0) }}
                    </div>
                </div>

                {{-- Subtotal (Before GST) --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600">Subtotal (Before GST)</div>
                    <div class="text-2xl font-bold mt-1 text-gray-900">
                        ₹{{ number_format((float) ($summary['subtotal_sum'] ?? 0), 2) }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                {{-- Paid vs Pending --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600 mb-2">Paid vs Pending</div>
                    <div class="flex items-baseline gap-8">
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Paid</div>
                            <div class="text-2xl font-bold text-green-600">
                                {{ (int) ($summary['paid_count'] ?? 0) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wide text-gray-500">Pending</div>
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ (int) ($summary['pending_count'] ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GST Trend (Last 6 Months) --}}
                <div class="rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition p-6">
                    <div class="text-sm text-gray-600 mb-4">GST Trend (Last 6 Months)</div>
                    <div class="w-full">
                        <canvas id="gstTrendChart" height="90"></canvas>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        GST Trend helps track tax collected month-wise.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const canvas = document.getElementById('gstTrendChart');
            if (!canvas) return;

            const labels = @json($gstTrendLabels ?? []);
            const data = @json($gstTrendData ?? []);

            const ctx = canvas.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'GST Collected',
                        data,
                        tension: 0.35,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#2563EB',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563EB',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 1.5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value = context.parsed.y ?? 0;
                                    return ' ₹' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                color: '#6B7280',
                            },
                        },
                        y: {
                            grid: {
                                color: 'rgba(209, 213, 219, 0.5)',
                            },
                            ticks: {
                                color: '#6B7280',
                                callback: function (value) {
                                    return '₹' + value;
                                },
                            },
                        },
                    },
                },
            });
        })();
    </script>
</x-app-layout>

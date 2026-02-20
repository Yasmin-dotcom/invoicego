<x-app-layout>
<div class="p-6">
    @php
        $invoiceLimitReached = auth()->check() && auth()->user()->invoiceLimitReached();
    @endphp

    @if(session('plan_limit'))
    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 flex justify-between items-center">
        <div>
            <div class="font-semibold text-red-700">
                You reached your invoice limit.
            </div>
            <div class="text-sm text-red-600">
                Upgrade your plan to create more invoices.
            </div>
        </div>
        <a href="{{ route('upgrade') }}"
           class="px-4 py-2 bg-black text-white rounded-lg text-sm">
            Upgrade Plan
        </a>
    </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Invoices</h1>

        <div class="flex items-center gap-2">

    {{-- Export Dropdown --}}
    <div class="relative" x-data="{ open:false }">

<button
    @click="open = !open"
    class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition text-sm">
    Export CSV ▼
</button>

<div
    x-show="open"
    @click.outside="open=false"
    class="absolute right-0 mt-2 bg-white border rounded-lg shadow w-40 z-50">

    {{-- All invoices --}}
    <a href="{{ route('invoices.export.csv', ['type' => 'all']) }}"
       class="block px-3 py-2 text-sm hover:bg-gray-100">
        All invoices
    </a>

    {{-- This page --}}
    <a href="{{ route('invoices.export.csv', ['type' => 'page']) }}"
       class="block px-3 py-2 text-sm hover:bg-gray-100">
        This page
    </a>

    {{-- Date range export --}}
    <form method="GET" action="{{ route('invoices.export.csv') }}" class="px-3 py-2 space-y-2">

    <input type="date" name="from"
        class="w-full border rounded px-2 py-1 text-sm">

    <input type="date" name="to"
        class="w-full border rounded px-2 py-1 text-sm">

    <button type="submit"
        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100">
        Date range
    </button>

</form>

</div>
</div>

    {{-- Create button --}}
    @if($invoiceLimitReached)
        <button
            type="button"
            disabled
            title="Invoice limit reached"
            class="bg-gray-400 text-white px-4 py-2 rounded-lg cursor-not-allowed">
            + Create Invoice
        </button>
    @else
        <a href="{{ route('invoices.create') }}"
           class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            + Create Invoice
        </a>
    @endif

</div>

    </div>


    {{-- Filters --}}
    @php
        $activeStatus = strtolower(trim((string) request('status', '')));
        $filters = [
            '' => 'All',
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
        ];
    @endphp


    <div x-data="{ search: '', selectAll: false, selected: [], allIds: @json($invoices->pluck('id')) }">

        <!-- Filters + Search Row -->
        <div class="mb-6 flex flex-wrap items-center gap-4">

            {{-- Status Filters --}}
            <div class="flex flex-wrap gap-2">
                @foreach($filters as $value => $label)
                    <a
                        href="{{ $value === '' ? route('invoices.index') : route('invoices.index', ['status' => $value]) }}"
                        class="px-3 py-1 rounded text-sm font-semibold
                        {{ $activeStatus === $value
                            ? 'bg-gray-900 text-white'
                            : 'bg-gray-100 text-black hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>


            {{-- Search Bar --}}
            <div class="relative w-full max-w-sm">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M8.5 3a5.5 5.5 0 104.473 8.73l3.398 3.399 1.414-1.414-3.399-3.398A5.5 5.5 0 008.5 3z"
                              clip-rule="evenodd" />
                    </svg>
                </span>

                <input
                    x-model="search"
                    type="text"
                    placeholder="Search invoice or client..."
                    class="w-full rounded-lg border border-gray-300 bg-white pl-10 pr-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                />
            </div>

        </div>



        {{-- Success --}}
        @if(session('success'))
            <div class="mb-5 px-4 py-3 rounded bg-green-100 text-green-800 border border-green-300">
                {{ session('success') }}
            </div>
        @endif



        {{-- Bulk Action Bar --}}
        <div
            x-show="selected.length > 0"
            x-cloak
            class="sticky top-0 z-20 mb-3 flex items-center justify-between rounded-md border bg-gray-50 px-4 py-2 shadow">

            <div class="text-sm text-gray-600">
                <span class="font-semibold text-gray-900" x-text="selected.length"></span> selected
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('invoices.bulk.send') }}" method="POST">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                        Send
                    </button>
                </form>

                <form action="{{ route('invoices.bulk.mark-paid') }}" method="POST">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                        class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white hover:bg-green-700 transition">
                        Mark Paid
                    </button>
                </form>

                <form action="{{ route('invoices.bulk.delete') }}" method="POST">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit"
                        class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700 transition">
                        Delete
                    </button>
                </form>
            </div>
        </div>



        {{-- Table --}}
        <div class="bg-white rounded-xl shadow overflow-visible">
            @include('invoices.partials.table')
        </div>

        {{-- ✅ CLEAN PAGINATION (single + right aligned only) --}}
        @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-6 flex justify-end">
            {{ $invoices->links() }}
        </div>
        @endif

    </div>

</div>
</x-app-layout>

<table class="w-full border border-gray-200 text-sm">

    {{-- ================= HEADER ================= --}}
    <thead class="sticky top-0 bg-white z-10 border-b border-gray-200">
        <tr>
            <th class="p-2 text-center w-10">
                <input
                    type="checkbox"
                    x-model="selectAll"
                    @change="selected = selectAll ? allIds.slice() : []"
                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0"
                    aria-label="Select all invoices">
            </th>
            <th class="p-2 text-left w-10">#</th>
            <th class="p-2 text-left">Invoice No</th>
            <th class="p-2 text-left">Client</th>
            <th class="p-2 text-left">Total</th>
            <th class="p-2 text-left">Status</th>
            <th class="p-2 text-center w-24">Actions</th>
        </tr>
    </thead>


    {{-- ================= BODY ================= --}}
    <tbody>

    @if($invoices->isEmpty())

        <tr>
            <td colspan="7" class="text-center p-6 text-gray-500">
                No invoices found
            </td>
        </tr>

    @else

        @foreach($invoices as $invoice)

        @php
            $status = strtolower((string) $invoice->status);
            $searchText = strtolower(trim(($invoice->invoice_number ?? '').' '.($invoice->client->name ?? '')));
        @endphp

        <tr
            class="border-t hover:bg-gray-50 transition cursor-pointer"
            data-search="{{ $searchText }}"
            x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())"
            x-cloak>

            {{-- Select --}}
            <td class="p-2 text-center">
                <input
                    type="checkbox"
                    name="ids[]"
                    value="{{ $invoice->id }}"
                    :checked="selected.includes({{ (int) $invoice->id }})"
                    @change="
                        if($event.target.checked){
                            selected.push({{ (int) $invoice->id }})
                        } else {
                            selected = selected.filter(id => id !== {{ (int) $invoice->id }})
                            selectAll = false
                        }
                    "
                    class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-0"
                    aria-label="Select invoice {{ $invoice->invoice_number }}">
            </td>

            {{-- # --}}
            <td class="p-2">
                {{ $loop->iteration }}
            </td>


            {{-- Invoice Number --}}
            <td class="p-2 font-medium">
                {{ $invoice->invoice_number }}
            </td>


            {{-- Client --}}
            <td class="p-2">
                {{ $invoice->client->name ?? '-' }}
            </td>


            {{-- Total --}}
            <td class="p-2">
                ₹{{ number_format($invoice->total, 2) }}
            </td>


            {{-- Status --}}
            <td class="p-2">
                <span class="px-2 py-1 rounded text-xs font-semibold
                    {{ $status === 'paid' ? 'bg-green-100 text-green-700' : ($status === 'sent' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </td>


            {{-- ================= ACTIONS (V2 PRO DROPDOWN) ================= --}}
            <td class="p-2 text-center relative w-24">

                <div x-data="{ open:false }" class="flex justify-center">

                    {{-- 3 dots button --}}
                    <button
                        @click="open = !open"
                        @keydown.escape.window="open = false"
                        class="w-10 h-10 flex items-center justify-center rounded-md hover:bg-gray-200 text-xl"
                        aria-label="Actions">
                        ⋮
                    </button>


                    {{-- Dropdown --}}
                    <div
                        x-show="open"
                        @click.away="open=false"
                        x-cloak
                        class="absolute right-0 top-full mt-2 w-44 bg-white border rounded-xl shadow-2xl text-sm z-50 overflow-hidden max-h-64 overflow-auto">


                        @if($status === 'draft')
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                View
                            </a>

                            <a href="{{ route('invoices.edit', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                Edit
                            </a>

                            <a href="{{ route('invoices.download', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                Download PDF
                            </a>

                            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-50">
                                    Mark Paid
                                </button>
                            </form>

                            <form action="{{ route('invoices.send', $invoice) }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-50">
                                    Send
                                </button>
                            </form>
                        @endif

                        @if($status === 'sent')
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                View
                            </a>

                            <a href="{{ route('invoices.edit', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                Edit
                            </a>

                            <a href="{{ route('invoices.download', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                Download PDF
                            </a>

                            <form action="{{ route('invoices.reminder', $invoice) }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-50">
                                    Reminder
                                </button>
                            </form>

                            <form action="{{ route('invoices.mark-paid', $invoice) }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-50">
                                    Mark Paid
                                </button>
                            </form>
                        @endif

                        @if($status === 'paid')
                            <a href="{{ route('invoices.show', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                View
                            </a>

                            <a href="{{ route('invoices.download', $invoice) }}"
                               class="block px-4 py-2 hover:bg-gray-50">
                                Download PDF
                            </a>

                            <form method="POST" action="{{ route('invoices.destroy', $invoice) }}">
                                @csrf
                                @method('DELETE')
                                <button class="w-full text-left px-4 py-2 hover:bg-gray-50 text-red-600">
                                    Delete
                                </button>
                            </form>
                        @endif

                    </div>

                </div>

            </td>

        </tr>

        @endforeach

    @endif

    </tbody>

</table>

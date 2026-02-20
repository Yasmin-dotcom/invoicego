<table class="w-full border border-gray-200 text-sm overflow-visible">

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
    <tbody class="overflow-visible">

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


            {{-- ================= ACTIONS (INLINE ICONS) ================= --}}
            <td class="p-2 w-44">
                <div class="flex justify-center items-center gap-2">
                    <a href="{{ route('invoices.show', $invoice) }}"
                       class="p-2 rounded-md transition duration-150 text-gray-400 hover:bg-gray-100 hover:text-blue-600 active:bg-blue-100 active:text-blue-700 focus:bg-blue-100 focus:outline-none"
                       title="View Invoice"
                       aria-label="View invoice">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </a>

                    @if($invoice->status != 'paid')
                    <a href="{{ route('invoices.edit', $invoice) }}"
                       class="p-2 rounded-md transition duration-150 text-gray-400 hover:bg-gray-100 hover:text-blue-600 active:bg-blue-100 active:text-blue-700 focus:bg-blue-100 focus:outline-none"
                       title="Edit Invoice"
                       aria-label="Edit invoice">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.12 2.12 0 1 1 3 3L7 19l-4 1 1-4Z"/>
                            <path d="m15 5 3 3"/>
                        </svg>
                    </a>
                    @endif

                    <a href="{{ route('invoices.download', $invoice) }}"
                       class="p-2 rounded-md transition duration-150 text-gray-400 hover:bg-gray-100 hover:text-blue-600 active:bg-blue-100 active:text-blue-700 focus:bg-blue-100 focus:outline-none"
                       title="Download PDF"
                       aria-label="Download invoice PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M12 18v-6"/>
                            <path d="m9 15 3 3 3-3"/>
                        </svg>
                    </a>

                    <a href="{{ route('invoices.send.preview', $invoice) }}"
                       class="p-2 rounded-md transition duration-150 text-gray-400 hover:bg-gray-100 hover:text-blue-600 active:bg-blue-100 active:text-blue-700 focus:bg-blue-100 focus:outline-none"
                       title="Send Email"
                       aria-label="Send invoice email">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m22 2-7 20-4-9-9-4Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>
                    </a>

                    @if($status !== 'paid')
                    <form method="POST"
                          action="{{ route('invoices.destroy', $invoice) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Delete this invoice? This cannot be undone.')"
                                class="p-2 rounded-md transition duration-150 text-gray-400 hover:bg-gray-100 hover:text-red-600 active:bg-red-100 active:text-red-700 focus:bg-red-100 focus:outline-none"
                                title="Delete Invoice"
                                aria-label="Delete invoice">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"/>
                                <path d="M8 6V4h8v2"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                <path d="M10 11v6"/>
                                <path d="M14 11v6"/>
                            </svg>
                        </button>
                    </form>
                    @endif
                </div>
            </td>

        </tr>

        @endforeach

    @endif

    </tbody>

</table>

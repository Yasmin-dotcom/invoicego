<x-app-layout>
<div class="p-6 max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Email Preview</h1>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded border border-red-200 bg-red-50 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded border border-green-200 bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 space-y-5">
        <div class="grid sm:grid-cols-2 gap-4 text-sm text-gray-700">
            <div><span class="font-semibold text-gray-900">Invoice #:</span> {{ $invoice->invoice_number }}</div>
            <div><span class="font-semibold text-gray-900">Client:</span> {{ $invoice->client->name ?? '-' }}</div>
            <div><span class="font-semibold text-gray-900">Total:</span> ₹{{ number_format($invoice->grand_total ?? $invoice->total, 2) }}</div>
            <div><span class="font-semibold text-gray-900">Email:</span> {{ $invoice->client->email ?? '-' }}</div>
        </div>

        <form action="{{ route('invoices.send.email', $invoice) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-800 mb-1">Email Subject</label>
                <input
                    type="text"
                    name="subject"
                    value="{{ old('subject', $previewSubject) }}"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-800 mb-1">Message</label>
                <textarea
                    name="message"
                    rows="6"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    required
                >{{ old('message', $previewMessage) }}</textarea>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="hidden" name="include_pdf" value="0">
                <input type="checkbox" name="include_pdf" value="1" class="rounded border-gray-300" @checked(old('include_pdf', true))>
                <span>Attach Invoice PDF</span>
            </label>

            <div>
                <label class="block text-sm font-medium text-gray-800 mb-1">Attach extra files (optional)</label>
                <input
                    type="file"
                    name="attachments[]"
                    multiple
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 file:mr-3 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-1.5 file:text-sm"
                >
                <p class="mt-1 text-xs text-gray-500">Up to 3 files, max 10MB each.</p>
            </div>

            <div class="flex flex-wrap gap-3">
                <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">
                    Send Email
                </button>

                <a href="{{ route('invoices.show', $invoice) }}"
                   class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 text-sm hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
</x-app-layout>

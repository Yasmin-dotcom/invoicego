@php
    $user = auth()->user();
@endphp

@if (
    auth()->check() &&
    in_array(auth()->user()->role, ['owner', 'client'], true) &&
    ! auth()->user()?->isPlanPro() &&
    auth()->user()->plan === 'free'
)
    <div class="border border-dashed border-gray-300 rounded-lg p-4 bg-gray-50">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium text-gray-800">
                    You’re on the Free plan
                </p>
                <p class="text-xs text-gray-600">
                    Upgrade to Pro for unlimited invoices & reminders.
                </p>
            </div>

            <a
                href="{{ route('upgrade') }}"
                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold
                       rounded-md bg-indigo-600 text-white hover:bg-indigo-700 transition">
                Upgrade
            </a>
        </div>
    </div>
@endif

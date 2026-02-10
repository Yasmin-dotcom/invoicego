@php
    $user = auth()->user();
@endphp

@if ($user)
    <span
        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
        {{ $user->isPro() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
        {{ $user->isPro() ? 'Pro Plan' : 'Free Plan' }}
    </span>
@endif

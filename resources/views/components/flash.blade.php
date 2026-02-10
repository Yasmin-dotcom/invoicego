@php
    $success = session('success');
    $error = session('error');
    $warning = session('warning');
    $info = session('info');
@endphp

@if ($success)
    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        {{ $success }}
    </div>
@endif

@if ($error)
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {{ $error }}
    </div>
@endif

@if ($warning)
    <div class="mb-4 rounded-lg border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
        {{ $warning }}
    </div>
@endif

@if ($info)
    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        {{ $info }}
    </div>
@endif

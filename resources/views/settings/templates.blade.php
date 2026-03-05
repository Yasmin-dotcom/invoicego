<x-app-layout>
<div class="p-6 max-w-5xl mx-auto">

    <h2 class="text-xl font-semibold mb-2">
        Default Invoice Template
    </h2>

    <p class="text-sm text-gray-500 mb-6">
        Choose default template. New invoices auto-use this.
    </p>

    @php
        $isPro = auth()->user()?->isPro();

        $templates = [
            'classic' => 'Classic',
            'minimal' => 'Minimal',
            'modern' => 'Modern',
            'gst' => 'GST Focused',
            'premium' => 'Premium',
        ];

        $freeAllowed = ['classic','minimal'];
        $current = $settings->default_template ?? 'classic';
    @endphp

    <form method="POST" action="{{ route('settings.templates.save') }}">
        @csrf

        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">

            @foreach($templates as $key => $label)

                @php
                    $locked = !$isPro && !in_array($key,$freeAllowed);
                    $selected = $current === $key;
                @endphp

                <label class="relative block">

                    {{-- RADIO --}}
                    <input
                        type="radio"
                        name="default_template"
                        value="{{ $key }}"
                        class="peer hidden"
                        {{ $selected ? 'checked' : '' }}
                        {{ $locked ? 'disabled' : '' }}
                    >

                    {{-- CARD --}}
                    <div class="
                        border-2 rounded-xl p-3 transition-all duration-200
                        {{ $locked ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer hover:shadow-md' }}
                        {{ $selected ? 'border-indigo-600 shadow-lg bg-indigo-50' : 'border-gray-200' }}
                        peer-checked:border-indigo-600
                        peer-checked:shadow-lg
                        peer-checked:bg-indigo-50
                    ">

                        {{-- IMAGE --}}
                        <img
                            src="{{ asset('template-previews/'.$key.'.png') }}"
                            onerror="this.onerror=null;this.src='{{ asset('template-previews/'.$key.'.jpeg') }}';"
                            class="rounded-lg shadow w-full h-48 object-contain bg-gray-50"
                            alt="{{ $label }} preview"
                        >

                        {{-- TITLE --}}
                        <p class="text-center mt-3 font-medium">
                            {{ $label }}

                            @if($locked)
                                <span class="text-orange-500 text-xs ml-1">🔒 Pro</span>
                            @endif
                        </p>

                        {{-- DEFAULT BADGE --}}
                        @if($selected)
                            <div class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded">
                                Default
                            </div>
                        @endif

                        {{-- PREVIEW LINK --}}
                        <a href="{{ route('templates.preview', $key) }}"
                           target="_blank"
                           class="block text-center text-indigo-600 text-xs mt-2 underline">
                            Preview PDF
                        </a>

                    </div>

                </label>

            @endforeach

        </div>

        <div class="mt-10 text-center">
            <button
                type="submit"
                class="px-8 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition">
                Save Default Template
            </button>
        </div>

    </form>

</div>
</x-app-layout>
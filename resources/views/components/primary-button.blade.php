<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-700 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-200 ease-out']) }}>
    {{ $slot }}
</button>

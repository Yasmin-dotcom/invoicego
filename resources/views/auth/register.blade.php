<x-guest-layout>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
    />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input
                    id="password"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                />
                <button
                    type="button"
                    onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Show or hide password"
                >
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative">
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full pr-10"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <button
                    type="button"
                    onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700 focus:outline-none"
                    aria-label="Show or hide password confirmation"
                >
                    <i class="bi bi-eye"></i>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            if (!input) return;

            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';

            const icon = button && button.querySelector ? button.querySelector('i') : null;
            if (icon) {
                icon.classList.toggle('bi-eye', !show);
                icon.classList.toggle('bi-eye-slash', show);
            }
        }
    </script>
</x-guest-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create Client</h1>
            <a href="{{ route('dashboard') }}"
               class="text-gray-600 hover:text-gray-900">
                ← Back to Dashboard
            </a>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone') }}"
                               inputmode="numeric"
                               maxlength="15"
                               pattern="[0-9]{10,15}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address Field -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <textarea name="address" 
                                  id="address" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reminder Enabled Field -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="reminder_enabled" 
                                   id="reminder_enabled" 
                                   value="1"
                                   {{ old('reminder_enabled', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-red-500 focus:ring-red-500 border-gray-300 rounded">
                            <label for="reminder_enabled" class="ml-2 block text-sm font-medium text-gray-700">
                                Enable payment reminders
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            When enabled, this client will receive automatic payment reminder emails for unpaid invoices.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('invoices.create') }}"
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                            Create Client
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

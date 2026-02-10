@extends('layouts.dashboard')

@section('title', 'Reminder Settings')

@section('content')
<div x-data="{ 
    remindersEnabled: {{ $settings->reminders_enabled ? 'true' : 'false' }},
    freeLimit: {{ $settings->free_reminder_limit }},
    proLimit: {{ $settings->pro_reminder_limit }},
    loading: false,
    success: false,
    message: ''
}" 
x-init="
    @if(session('success'))
        success = true;
        message = '{{ session('success') }}';
        setTimeout(() => success = false, 3000);
    @endif
">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Reminder Settings</h1>
    </div>

    <!-- Success Toast -->
    <div x-show="success" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
         role="alert">
        <span x-text="message"></span>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <form method="POST" action="{{ route('admin.settings.reminders') }}" 
              @submit.prevent="
                  loading = true;
                  const formData = new FormData();
                  formData.append('reminders_enabled', remindersEnabled ? 1 : 0);
                  formData.append('free_reminder_limit', parseInt(freeLimit));
                  formData.append('pro_reminder_limit', parseInt(proLimit));
                  formData.append('_token', '{{ csrf_token() }}');
                  
                  fetch('{{ route('admin.settings.reminders') }}', {
                      method: 'POST',
                      headers: {
                          'X-Requested-With': 'XMLHttpRequest',
                          'Accept': 'application/json'
                      },
                      body: formData
                  })
                  .then(response => response.json())
                  .then(data => {
                      loading = false;
                      if (data.success) {
                          success = true;
                          message = data.message || 'Settings updated successfully.';
                          setTimeout(() => success = false, 3000);
                      }
                  })
                  .catch(error => {
                      loading = false;
                      console.error('Error:', error);
                      // Fallback to regular form submission on error
                      this.$el.submit();
                  });
              ">
            @csrf

            <div class="space-y-6">
                <!-- Reminders Enabled Toggle -->
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Enable Payment Reminders
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            When enabled, the system will automatically send payment reminders to clients.
                        </p>
                    </div>
                    <button type="button"
                            @click="remindersEnabled = !remindersEnabled"
                            :class="remindersEnabled ? 'bg-red-500' : 'bg-gray-300'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <span :class="remindersEnabled ? 'translate-x-6' : 'translate-x-1'"
                              class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                    <input type="hidden" name="reminders_enabled" :value="remindersEnabled ? 1 : 0">
                </div>

                <!-- Free Plan Reminder Limit -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <label for="free_reminder_limit" class="block text-sm font-medium text-gray-700 mb-2">
                        Free Plan Reminder Limit
                    </label>
                    <p class="text-sm text-gray-500 mb-3">
                        Maximum number of reminders that can be sent per invoice for free plan users.
                    </p>
                    <input type="number"
                           id="free_reminder_limit"
                           x-model.number="freeLimit"
                           min="0"
                           max="100"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    <p class="text-xs text-gray-400 mt-1">Range: 0-100</p>
                </div>

                <!-- Pro Plan Reminder Limit -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <label for="pro_reminder_limit" class="block text-sm font-medium text-gray-700 mb-2">
                        Pro Plan Reminder Limit
                    </label>
                    <p class="text-sm text-gray-500 mb-3">
                        Maximum number of reminders that can be sent per invoice for pro plan users.
                    </p>
                    <input type="number"
                           id="pro_reminder_limit"
                           x-model.number="proLimit"
                           min="1"
                           max="100000"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500">
                    <p class="text-xs text-gray-400 mt-1">Range: 1-100,000</p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            :disabled="loading"
                            :class="loading ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-6 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <span x-show="!loading">Save Settings</span>
                        <span x-show="loading">Saving...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js CDN (if not already included) -->
@if(!isset($alpineIncluded))
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endif
@endsection

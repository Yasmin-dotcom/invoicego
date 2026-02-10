<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReminderSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Admin-only access should be enforced by route middleware.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reminders_enabled' => ['required', 'boolean'],

            // Accept "3,0,-2" style input; we normalize in the controller.
            'default_reminder_days' => [
                'required',
                'string',
                'max:255',
                'regex:/^\s*-?\d+\s*(\s*,\s*-?\d+\s*)*$/',
            ],

            'email_enabled' => ['required', 'boolean'],
            'whatsapp_enabled' => ['required', 'boolean'],
            'sms_enabled' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'default_reminder_days.regex' => 'Default reminder days must be a comma-separated list of integers (example: 3,0,-2).',
        ];
    }
}

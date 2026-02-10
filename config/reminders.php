<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Reminder Configuration
    |--------------------------------------------------------------------------
    |
    | Configure when and how often payment reminders are sent for unpaid
    | invoices.
    |
    */

    // Number of days before due date to send first reminder
    'before_days' => env('REMINDER_BEFORE_DAYS', 3),

    // Number of days between overdue reminders
    'overdue_interval_days' => env('REMINDER_OVERDUE_INTERVAL_DAYS', 3),

    // Maximum number of reminders to send per invoice
    'max_reminders' => env('REMINDER_MAX_COUNT', 5),
];

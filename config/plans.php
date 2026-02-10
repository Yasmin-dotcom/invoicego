<?php

return [
    'free' => [
        // Max invoices a free user can create (null = unlimited)
        'invoice_limit' => 5,

        // Max reminders per calendar month for free users
        'monthly_reminders' => 5,
    ],

    'pro' => [
        // Unlimited invoices for Pro users
        'invoice_limit' => null,

        // -1 means unlimited
        'monthly_reminders' => -1,
    ],
];


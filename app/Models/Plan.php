<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'invoice_limit',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'integer',
        'invoice_limit' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
    ];
}


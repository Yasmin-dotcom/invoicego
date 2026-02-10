<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        // Stored column (legacy + DB schema)
        'description',
        // V1-friendly alias (mapped to description via mutator)
        'name',
        'quantity',
        'price',
        // Computed (not stored)
        'total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'name',
        'total',
    ];

    /**
     * InvoiceItem belongs to an Invoice
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * V1 alias: "name" maps to legacy "description" column.
     */
    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['description'] ?? '');
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['description'] = (string) $value;
    }

    /**
     * Computed total = quantity * price (not persisted).
     */
    public function getTotalAttribute(): float
    {
        $qty = (float) ($this->attributes['quantity'] ?? 0);
        $price = (float) ($this->attributes['price'] ?? 0);

        return round($qty * $price, 2);
    }

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            // Keep legacy column in sync if callers send "name"
            if (
                array_key_exists('name', $item->attributes)
                && ! array_key_exists('description', $item->attributes)
            ) {
                $item->attributes['description'] = (string) $item->attributes['name'];
                unset($item->attributes['name']);
            }
        });
    }
}

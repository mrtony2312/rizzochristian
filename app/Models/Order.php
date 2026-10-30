<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 'reference', 'name', 'first_name', 'last_name', 'company',
        'email', 'phone', 'country', 'address', 'address_2', 'city', 'state',
        'postal_code', 'notes', 'payment_method', 'total', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function countryName(): string
    {
        $countries = config('billing.countries', []);

        return $countries[$this->country] ?? (string) $this->country;
    }

    public function provinceName(): ?string
    {
        if ($this->state === null || $this->state === '') {
            return null;
        }

        $provinces = config('billing.provinces', []);

        return $provinces[$this->state] ?? $this->state;
    }

    public function paymentLabel(): string
    {
        $methods = config('billing.payment_methods', []);

        return $methods[$this->payment_method] ?? ucfirst((string) $this->payment_method);
    }

    public function statusLabel(): string
    {
        $statuses = config('billing.statuses', []);

        return $statuses[$this->status] ?? ucfirst((string) $this->status);
    }

    public function isBankTransfer(): bool
    {
        return in_array($this->payment_method, ['bonifico', 'vorkasse'], true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'is_default',
        'address_type', // billing, shipping, both
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the user that owns the address.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get default addresses
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get addresses by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('address_type', $type);
    }

    /**
     * Get the full address as a formatted string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->street}, {$this->city}, {$this->state} {$this->zip}, {$this->country}";
    }
}

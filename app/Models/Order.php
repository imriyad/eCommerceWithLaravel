<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'grand_total',
        'item_count',
        'payment_method',
        'payment_status',
        'name',
        'email',
        'address',
        'city',
        'postal_code',
        'notes',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
}
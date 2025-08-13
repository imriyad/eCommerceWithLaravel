<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← add this

class Product extends Model
{
    use HasFactory; // ← and this

    protected $fillable = [
        'name', 'description', 'price', 'brand', 'stock', 'sku', 'is_active',
        'image', 'category_id', 'discount_price', 'tax', 'weight', 'dimensions',
        'tags', 'warranty', 'specifications', 'color', 'size', 'status'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}

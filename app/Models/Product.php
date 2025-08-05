<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
protected $fillable = [
    'name', 'description', 'price', 'brand', 'stock', 'sku', 'is_active', 'image', 'category_id'
];
}

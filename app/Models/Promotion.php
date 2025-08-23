<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name', 'type', 'value', 'start_date', 'end_date', 'applicable_products', 'status'
    ];

    protected $casts = [
        'applicable_products' => 'array', // Store product IDs as JSON
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];
    public function products()
{
    return $this->belongsToMany(Product::class, 'promotion_product'); // pivot table
}
}

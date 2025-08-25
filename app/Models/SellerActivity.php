<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerActivity extends Model
{
    use HasFactory;

    protected $table = 'seller_activity';

    protected $fillable = ['seller_id', 'message'];

    public function seller()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];


    public function user()
    {
        return $this->hasMany(User::class);
    }

    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}

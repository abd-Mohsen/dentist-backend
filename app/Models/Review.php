<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'product_id',
        'rate',
        'comment'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'order_id',
        'status',
        'supplier_id'
    ];


    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'price');
    }

}

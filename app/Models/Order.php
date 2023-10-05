<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'customer_id',
        //'supplier_id',
    ];


    public function subOrders()
    {
        return $this->hasMany(SubOrder::class);
    }


    public function products()
    {
        return $this->hasManyThrough(Product::class, SubOrder::class);
    }


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

}

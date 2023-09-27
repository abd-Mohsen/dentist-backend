<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', // change to title
        'description',
        'price',
        'weight',
        'length',
        'width',
        'height',
        'upc',
        'sku',
        'quantity',
        'sell_quantity',
        'max_purchase_qty',
        'min_purchase_qty',
        'active',
        'owner_id',
        'brand_id',
        //list of categories (fill in pivot table)
        //list of images (fill in pivot table)
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            //'owner' => $this->owner?->name,
            //barcode
        ];
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }

}

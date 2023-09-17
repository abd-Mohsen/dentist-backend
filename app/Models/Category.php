<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'parent_id',
        'image_id',
    ];

    public function image(){
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    //if a category has a parent => cant add it as a parent to a new category
    //if a category dont have a parent => cant add products to it
}

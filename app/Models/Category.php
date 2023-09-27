<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'parent_id',
        'image_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($category) {
            if ($category->children()->exists()) {
                throw new \Exception("Cannot delete category with child categories.");
            }
        });
    }


    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            //'parent' => $this->parent?->title,
            //barcode
        ];
    }


    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    
    public function image()
    {
        return $this->belongsTo(Image::class);
    }


    public function products()
    {
        return $this->belongsToMany(Product::class); //wrong realtion ??
    }

    
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    //if a category has a parent => cant add it as a parent to a new category
    //if a category dont have a parent => cant add products to it
}

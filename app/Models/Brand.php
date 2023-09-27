<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Brand extends Model
{
    use HasFactory, SoftDeletes, Searchable;
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'image_id',
    ];

    public function toSearchableArray()
    {
        return ['title' => $this->title];
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
        'image_id',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

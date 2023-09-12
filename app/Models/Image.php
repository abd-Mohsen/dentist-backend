<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    //protected $table = 'images';

    protected $fillable = [
        'path',
        'type',
    ];

    protected function path(): Attribute {
        return Attribute::make(get: function($val){
            return $val ? 'storage/profile/' . $val : null;
        });
    }
}

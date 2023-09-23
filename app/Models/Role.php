<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    //protected $table = 'roles';

    protected $fillable = [
        'title',
    ];

    public const ADMIN = 1;
    public const DENTIST = 2;
    public const SUPPLIER = 3;
}

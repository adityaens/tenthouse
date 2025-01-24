<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'user_id',
        'cat_id',
        'description',
        'price',
        'quantity',
        'product_condition',
        'status',
    ];

    protected $hidden = [
        'name',
        'user_id',
        'cat_id',
        'description',
        'price',
        'quantity',
        'product_condition',
        'status',
    ];
}

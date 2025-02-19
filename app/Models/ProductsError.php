<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsError extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'sku',
        'user_id',
        'cat_id',
        'description',
        'price',
        'quantity',
        'used_qty',
        'rem_qty',
        'product_condition',
        'status',
    ];
}

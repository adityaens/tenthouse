<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'categories';

    // Specify the fillable fields
    protected $fillable = ['name', 'cat_status'];
}

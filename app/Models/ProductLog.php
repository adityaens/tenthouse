<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id',
        'change_type',
        'old_values',
        'new_values',
        'remarks',	
    ];
    
    protected $hidden = [
        'product_id',
        'user_id',
        'change_type',
        'old_values',
        'new_values',
        'remarks',	
    ];
}

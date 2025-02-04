<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    public $fillable = [
        'order_id',
        'old_data',
        'new_data'
    ];

    public $hidden = [
        'order_id',
        'old_data',
        'new_data'
    ];
}

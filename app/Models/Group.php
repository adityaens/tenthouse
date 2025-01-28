<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'discount',        
        'description',       
        'status',
    ];

    protected $hidden = [
        'name',
        'discount',        
        'description',       
        'status',
    ];
}

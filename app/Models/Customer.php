<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'roleId',
        'group_id',        
        'email',       
        'password',       
        'name',       
        'mobile',       
        'address',       
        'status',
    ];
    protected $hidden = [
        'roleId',
        'group_id',        
        'email',       
        'password',       
        'name',       
        'mobile',       
        'address',       
        'status',
    ];
}

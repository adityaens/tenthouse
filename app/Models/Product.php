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
        'used_qty',
        'rem_qty',
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
        'used_qty',
        'rem_qty',
        'product_condition',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userId');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'cat_id', 'id');
    }
}

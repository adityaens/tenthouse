<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->sku = self::generateSKU($product);
        });
    }

    // Generate a unique SKU
    public static function generateSKU($product)
    {

        return strtoupper(Str::random(3)) . '-' . strtoupper(Str::random(4)) . '-' . Str::random(3);
       
    }
}

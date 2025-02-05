<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $fillable = [
        'order_id',
        'user_id',
        'payment_method_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'due_date',
        'status',
        'delivered_by',
        'booking_date_from',
        'booking_date_to',
        'remarks'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'userId');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentModel::class, 'payment_method_id', 'id');
    }
}

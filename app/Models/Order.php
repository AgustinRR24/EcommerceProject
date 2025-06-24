<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';    

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'promo_code_id',
        'discount',
        'tax',
        'total',
        'payment_method',
        'payment_status',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'shipping_zipcode',
        'shipping_phone',
        'notes'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod():BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function promoCode():BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function orderDetails():HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}

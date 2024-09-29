<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'tracking_no',
        'first_name',
        'last_name',
        'phone_no',
        'email',
        'district',
        'city',
        'postal_code',
        'shipping_address',
        'grand_total',
    ];

    public function orderitems(){
        return $this->hasMany(OrderItems::class,'order_id');
    }

    public function storeorders(){
        return $this->hasMany(StoreOrder::class,'order_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function payment(){
        return $this->belongsTo(Payment::class,'payment_id');
    }
}

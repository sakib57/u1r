<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Store;

class StoreOrder extends Model
{
    use HasFactory;

    protected $fillable= [
        'order_id',
        'store_id',
        'shipping_cost',
        'discount_type',
        'discount',
        'shipping_status',
        'total',
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function store(){
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function orderitems(){
        return $this->hasMany(OrderItems::class,'store_order_id');
    }
}

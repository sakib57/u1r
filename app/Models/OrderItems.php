<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'store_order_id',
        'product_id',
        'product_variant_id',
        'quantity',
    ];

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable= [
        'user_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'sub_total'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}

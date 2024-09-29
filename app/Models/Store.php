<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'email',
        'address',
        'city',
        'postal_code',
        'phone_no',
        'user_id'
    ];

    public function storeCategories()
    {
        return $this->hasMany(StoreCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

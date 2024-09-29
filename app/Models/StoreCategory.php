<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    use HasFactory;

    protected $fillable= [
        'store_id',
        'main_category_id',
    ];

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }
}

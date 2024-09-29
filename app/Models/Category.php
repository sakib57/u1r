<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubCategory;
use App\Models\MainCategory;

class Category extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'is_active',
        'main_category_id',
        'sub_category_id'
    ];

    public function maincategory()
    {
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }

     public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}

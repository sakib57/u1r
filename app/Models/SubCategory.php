<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MainCategory;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'is_active',
        'main_category_id',
    ];

    public function maincategory(){
        return $this->belongsTo(MainCategory::class, 'main_category_id');
    }
}

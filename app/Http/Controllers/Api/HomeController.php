<?php

// namespace App\Http\Controllers\api;

// use App\Http\Controllers\Controller;
// use App\Models\SubCategory;
// use App\Models\Product;
// use Illuminate\Http\Request;

// class HomeController extends Controller
// {
//     /**
//     *@OA\Get(
//      *     path="/api/home/data",
//      *     summary="Get Dashboard product related data",
//      *     tags={"Dashboard"},
//      *     @OA\Response(response="200", description="Get Data Successfull")
//      * )
//      */
//     public function index(){
//         $featured_sub_cats = SubCategory::where('is_featured', true)->get();
//         $displayed_sub_cats = SubCategory::where('is_displayed', true)->get();
//         $from = date('Y-m-d h:m:i', strtotime('-5 days'));
//         $to = date('Y-m-d h:m:i');
//         $new_araival = Product::whereBetween('created_at',[$from, $to])->get()->take(30);
//         $data['new_arrival'] = $new_araival;
//         $data['displayed_sub_cats'] = $displayed_sub_cats;
//         foreach($featured_sub_cats as $featured_sub_cat){
//             $key = strtolower($featured_sub_cat->sub_cate_name);
//             $key = str_replace(' ', '_', $key);
//             $data[$key] = $featured_sub_cat;
//             $data[$key]["products"] = Product::where('sub_category_id',$featured_sub_cat->id)->get()->take(8);
//         }
        
//         return response([
//             'data'=> $data,
//             'message'=> "Success!",
//         ],200);
//     }
// }

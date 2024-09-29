<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductVariantImage;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;
use Exception;

class ProductController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/products",
     *     summary="Get Product list",
     *     tags={"Products"},
     *     @OA\Parameter(
     *        name="store_id",
     *        in="query",
     *        description="Store Id",
     *     ),
     *     @OA\Parameter(
     *        name="brand_id",
     *        in="query",
     *        description="Brand Id",
     *     ),
     *     @OA\Response(response="200", description="Get Data Successfull")
     * )
     */
    public function index(Request $request)
    {
        $where = [];
        // Filter by store id
        if($request->store_id){
            array_push($where, ['store_id','=',$request->store_id]);
        }
        // Filter by brand Iid
        if($request->brand_id){
            array_push($where, ['brand_id','=',$request->brand_id]);
        }
        // Add category chain into search quary
        if($request->refinementList && count($request->refinementList["cats"]) > 0){
            // $searchedProducts = collect(new Product);
            foreach($request->refinementList["cats"] as $cat){
                $v = explode(" > ",$cat);
                foreach($v as $key => $t){
                    if($key == 0){
                        $main_cat_name = $t;
                        $main_cat_collection = MainCategory::where('name',$main_cat_name)->first();
                        if($main_cat_collection){
                            array_push($where, ['main_category_id','=',$main_cat_collection->id]);
                        }
                    }
                    if($key == 1){
                        $sub_cat_name = $t;
                        $sub_cat_collection = SubCategory::where('name',$sub_cat_name)->first();
                        if($sub_cat_collection){
                            array_push($where, ['sub_category_id','=',$sub_cat_collection->id]);
                        }
                    }
                    if($key == 2){
                        $cat_name = $t;
                        $cat_collection = Category::where('name',$cat_name)->first();
                        if($cat_collection){
                            array_push($where, ['category_id','=',$cat_collection->id]);
                        }
                    }
                }
            }
        }

        
        if(count($where) > 0){
            // $new_prod_collection = collect(new Product);
            $searchedProducts = Product::where($where)->with(['MainCategory' 
            => function($q){ return $q->select('id','name'); },
                'subcategory'
                => function($q){ return $q->select('id','name'); }
                , 'brand' 
            => function($q){ return $q->select('id','name'); },
            'category'
            => function($q){ return $q->select('id','name'); },
            'store','images','variants','variants.color','variants.size','variants.image'])->get();            
            // $searchedProducts = $searchedProducts->merge($new_prod_collection);
            return response()->json($searchedProducts, 200);
        }
        
        
        

        $products = Product::with(['maincategory' 
        => function($q){ return $q->select('id','name'); },
         'subcategory'
         => function($q){ return $q->select('id','name'); }
         , 'brand' 
        => function($q){ return $q->select('id','name'); },
        'category'
        => function($q){ return $q->select('id','name'); },
        'store','images','variants','variants.color','variants.size','variants.image'])->get();
        return response()->json($products, 200);
    }

    /**
    *@OA\Post(
     *     path="/api/products",
     *     summary="Add a product",
     *     tags={"Products"},
     *    @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"name","price","cros_out_price","main_category_id","sub_category_id","category_id","brand_id","store_id"},
    *            @OA\Property(property="name"),
    *            @OA\Property(property="price", type="double"),
    *            @OA\Property(property="cros_out_price", type="double"),
    *            @OA\Property(property="stock", type="integer"),
    *            @OA\Property(property="description"),
    *            @OA\Property(property="images",type="array" ,
    *               @OA\Items(
    *                  @OA\Property(property="image"),
    *               )
    *            ),
    *            @OA\Property(property="main_category_id", type="integer"),
    *            @OA\Property(property="sub_category_id", type="integer"),
    *            @OA\Property(property="category_id", type="integer"),
    *            @OA\Property(property="brand_id", type="integer"),
    *            @OA\Property(property="store_id", type="integer"),
    *            @OA\Property(property="variants", type="array",
    *               @OA\Items(
    *                  @OA\Property(property="color_id", type="integer"),
    *                  @OA\Property(property="size_id", type="integer"),
    *                  @OA\Property(property="stock", type="integer"),
    *                  @OA\Property(property="image"),
    *               )
    *            )
    *          ),
    *    ),
     *     @OA\Response(response="201", description="Product created successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function store(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'cros_out_price' => 'required|numeric|min:0',
                'stock' => 'numeric|min:0',
                'description' => 'required|string',
                'images' => 'array',
                'main_category_id' => 'required|integer|exists:main_categories,id',
                'sub_category_id' => 'required|integer|exists:sub_categories,id',
                'category_id' => 'required|integer|exists:categories,id',
                'brand_id' => 'required|integer|exists:brands,id',
                'store_id' => 'required|integer|exists:stores,id',
            ]);
            
            $data = $request->all();
            DB::beginTransaction();
            $product = Product::create($data);
            // Create product images
            if(count($request->images) > 0){
                foreach($request->images as $image){
                    $image_data = [
                        "product_id" => $product->id,
                        "image" => $image['image']
                    ];
                    ProductImage::create($image_data);
                }
            }
            // Create product variations
            if(count($request->variants) > 0){
                foreach($request->variants as $variant){
                    $variation_data = [
                        "product_id" => $product->id,
                        "color_id" => array_key_exists('color_id', $variant) ? $variant['color_id'] : null,
                        "size_id" => array_key_exists('size_id', $variant) ? $variant['size_id'] : null,
                        "stock" => $variant['stock']
                    ];
                    $product_variant = ProductVariant::create($variation_data);
                    // Create product variation images
                    if(array_key_exists('image', $variant)){
                        
                        $variant_image_data = [
                            "product_variant_id" => $product_variant->id,
                            "image" => $variant['image']
                        ];
                        ProductVariantImage::create($variant_image_data);
                    }
                }
            }
            DB::commit();
            return response([
                'message'=> "Product created successfully!",
            ],201);
        } catch (\Exception $e) {
            DB::rollback();
            return response([
                'message'=> "Something went wrong!",
                'error'=>$e
            ],500);
        }
    }

    /**
    *@OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get Product with id",
     *     tags={"Products"},
     *     @OA\Response(response="200", description="Get Data Successfull")
     * )
     */
    public function show($id){
        $products = Product::where('id',$id)->with(['maincategory' 
        => function($q){ return $q->select('id','name'); },
         'subcategory'
         => function($q){ return $q->select('id','name'); }
         , 'brand' 
        => function($q){ return $q->select('id','name'); },
        'Category'
        => function($q){ return $q->select('id','name'); },
         'store','images','variants','variants.color','variants.size','variants.image'])->first();
        return response()->json($products, 200);
    }


    /**
    *@OA\Patch(
    *     path="/api/products/{id}",
    *     summary="Update a product",
    *     tags={"Products"},
    *    @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"name","price","cros_out_price","main_category_id","sub_category_id","category_id","brand_id","store_id"},
    *            @OA\Property(property="name"),
    *            @OA\Property(property="price", type="integer"),
    *            @OA\Property(property="cros_out_price", type="integer"),
    *            @OA\Property(property="stock", type="integer"),
    *            @OA\Property(property="description"),
    *            @OA\Property(property="images",type="array" ,
    *               @OA\Items(
    *                  @OA\Property(property="image"),
    *               )
    *            ),
    *            @OA\Property(property="main_category_id", type="integer"),
    *            @OA\Property(property="sub_category_id", type="integer"),
    *            @OA\Property(property="category_id", type="integer"),
    *            @OA\Property(property="brand_id", type="integer"),
    *            @OA\Property(property="store_id", type="integer"),
    *            @OA\Property(property="variants", type="array",
    *               @OA\Items(
    *                  @OA\Property(property="color_id", type="integer"),
    *                  @OA\Property(property="size_id", type="integer"),
    *                  @OA\Property(property="stock", type="integer"),
    *                  @OA\Property(property="image"),
    *               )
    *            )
    *          ),
    *    ),
    *     @OA\Response(response="201", description="Product created successfully"),
    *     @OA\Response(response="422", description="Validation errors")
    * )
    */
    public function update(Request $request, $id){
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'cros_out_price' => 'required|numeric|min:0',
                'stock' => 'required|numeric|min:0',
                'description' => 'required|string',
                'images' => 'array',
                'main_category_id' => 'required|integer|exists:main_categories,id',
                'sub_category_id' => 'required|integer|exists:sub_categories,id',
                'category_id' => 'required|integer|exists:categories,id',
                'brand_id' => 'required|integer|exists:brands,id',
                'store_id' => 'required|integer|exists:stores,id',
           ]);
   
           $data = $request->all();
           DB::beginTransaction();
           // Deletion of product images
           foreach($product->images as $image){
            ProductImage::where('product_id',$product->id)->delete();
           }
           // Deletion of variant images
           foreach($product->variants as $variant){
            if($variant->image !== null){
                ProductVariantImage::where('product_variant_id',$variant->id)->delete();
            }
           }
           ProductVariant::where('product_id',$product->id)->delete();
           
           $product->update($data);

           // Create product images
           if(count($request->images) > 0){
            foreach($request->images as $image){
                $image_data = [
                    "product_id" => $product->id,
                    "image" => $image['image']
                ];
                ProductImage::create($image_data);
            }
        }
           // Create product variations
           if(count($request->variants) > 0){
            foreach($request->variants as $variant){
                $variation_data = [
                    "product_id" => $product->id,
                    "color_id" => array_key_exists('color_id', $variant) ? $variant['color_id'] : null,
                    "size_id" => array_key_exists('size_id', $variant) ? $variant['size_id'] : null,
                    "stock" => $variant['stock']
                ];
                $product_variant = ProductVariant::create($variation_data);

                // return response([
                //     'message'=> $product_variant,
                // ],201);

                // Create product variation images
                if(array_key_exists('image', $variant)){
                    $image_data = [
                        "product_variant_id" => $product_variant->id,
                        "image" => $variant['image']
                    ];
                    // return response([
                    //     'message'=> $image_data,
                    // ],201);
                    ProductVariantImage::create($image_data);
                }
            }
        }
        DB::commit();
        return response([
            'message'=> "Product updated successfully!",
        ],201);

        } catch (\Exception $e) {
            DB::rollback();
            return response([
                'message'=> "Something went wrong!",
                'error'=>$e
            ],500);
        }
       
    }



//     /**
//     *@OA\Delete(
//      *     path="/api/deleteProducts/{id}",
//      *     summary="Delete Product with id",
//      *     tags={"Products"},
//      *     @OA\Response(response="200", description="Data deleted Successfully")
//      * )
//      */
//     public function destroy($id)
//     {
//         $product = Product::find($id);

//         if (!$product) {
//             return response()->json(['message' => 'Product not found'], 404);
//         }
//         try {
//             DB::beginTransaction();
//             ProductVariation::where('product_id',$product->id)->delete();
//             $product->delete();
//             DB::commit();
//             return response()->json(['message' => 'Product deleted successfully'], 200);
//         } catch (\Throwable $th) {
//             DB::rollback();
//             return response()->json(['message' => 'Could not delete product'], 500);
//         }
//     }
}

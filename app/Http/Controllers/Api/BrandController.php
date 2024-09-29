<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{

    /**
    *@OA\Get(
     *     path="/api/brands",
     *     summary="Get Brand List",
     *     tags={"Brand"},
     *      @OA\Parameter(
     *         name="main_category",
     *         in="query",
     *         description="Main category id",
     *     ),
     *     @OA\Parameter(
     *         name="sub_category",
     *         in="query",
     *         description="Sub category id",
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Category id",
     *     ),
     *     @OA\Response(response="200", description="Get Brands successfully")
     * )
     */
    public function index(Request $request){
        $where = [];
        if($request->main_category){
            $where['main_category_id'] = $request->main_category;
        }
        if($request->sub_category){
            $where['sub_category_id'] = $request->sub_category;
        }
        if($request->category){
            $where['category_id'] = $request->category;
        }
        $brands = Brand::where($where)->get();
        return response()->json($brands, 200);
    }


    /**
    *@OA\Post(
     *     path="/api/brands",
     *     summary="Create Brand",
     *     tags={"Brand"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name"},
     *            @OA\Property(property="name"),
     *            @OA\Property(property="image"),
     *            @OA\Property(property="is_active", type="boolean"),
     *            @OA\Property(property="main_category_id", type="integer"),
     *            @OA\Property(property="sub_category_id", type="integer"),
     *            @OA\Property(property="category_id", type="integer"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Brand created Successfully"),
     * )
    */
    public function store(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:brands',
                'image' => 'nullable|string',
                'is_active' => 'boolean',
            ]);
            $data = $request->all();
            $brand = Brand::create($data);
            return response([
                'message'=> "Brand Created Successfully!",
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }


    /**
    *@OA\Get(
     *     path="/api/brands/{id}",
     *     summary="Get Brand List",
     *     tags={"Brand"},
     *     @OA\Response(response="200", description="Get Brand successfully")
     * )
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => ' Brand not found'], 404);
        }

        return response()->json($brand, 201);
    }


    /**
    *@OA\Patch(
     *     path="/api/brands/{id}",
     *     summary="Update Brand",
     *     tags={"Brand"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="image"),
     *            @OA\Property(property="is_active"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Brand Updated Successfully")
     * )
    */
    public function update(Request $request, $id){
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $request->validate([
            'name' => 'string',
            'image' => 'nullable|string',
            'is_active' => 'boolean',

        ]);

        $data = $request->only('name','image','is_active');
        $brand->update($data);
        return response([
            'message'=> "Brand Updated Successfully!",
        ],201);
    }


    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }

        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully'], 201);
    }
}

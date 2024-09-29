<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    //
    /**
    *@OA\Get(
     *     path="/api/categories",
     *     summary="Get Category List",
     *     tags={"Category"},
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
     *     @OA\Response(response="200", description="Get Category successfully")
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
        $categories = Category::where($where)->with(['maincategory', 'subcategory'])->get();
        return response()->json($categories, 200);
    }

    // Store a newly created main category
    /**
    *@OA\Post(
     *     path="/api/categories",
     *     summary="Create Category",
     *     tags={"Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name","main_category_id","sub_category_id"},
     *            @OA\Property(property="name"),
     *            @OA\Property(property="main_category_id"),
     *            @OA\Property(property="sub_category_id"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Category created Successfully"),
     * )
    */
    public function store(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'main_category_id' => 'required',
                'sub_category_id' => 'required',
            ]);
            $data = $request->all();
            $category = Category::create($data);
            return response([
                'message'=> "Category Created Successfully!",
            ],201);
        } catch (QueryException $exception) {
            if ($exception->errorInfo[1] == 1062) {
                throw ValidationException::withMessages(['name' => 'The name has already been taken.']);
            } else {
                throw $exception;
            }
        }
    }


    /**
    *@OA\Get(
     *     path="/api/categories/{id}",
     *     summary="Get Category By ID",
     *     tags={"Category"},
     *     @OA\Response(response="200", description="Get Category successfully")
     * )
     */
    public function show($id)
    {
        $category = Category::with(['maincategory', 'subcategory'])->find($id);

        if (!$category) {
            return response()->json(['message' => ' Category not found'], 404);
        }

        return response()->json($category, 200);
    }


    /**
    *@OA\Patch(
     *     path="/api/categories/{id}",
     *     summary="Update Category",
     *     tags={"Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="is_active"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Category Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);
        $data = $request->all();
        $category->update($data);
        return response()->json($category, 200);
    }

    // Remove the specified main category from storage
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}

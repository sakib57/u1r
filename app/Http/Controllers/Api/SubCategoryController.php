<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Exception;
use Illuminate\Validation\ValidationException;


class SubCategoryController extends Controller
{

     // List all main categories
    /**
    *@OA\Get(
     *     path="/api/sub-category",
     *     summary="Get Sub Category List",
     *     tags={"Sub Category"},
     *      @OA\Parameter(
     *         name="main_category",
     *         in="query",
     *         description="Main category id",
     *     ),
     *     @OA\Response(response="200", description="Get Sub Category successfully")
     * )
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->main_category){
            $where['main_category_id'] = $request->main_category;
        }
        $subCategories = SubCategory::where($where)->with('maincategory')->get();
        return response()->json($subCategories, 200);
    }

    // Store a newly created main category
    /**
    *@OA\Post(
     *     path="/api/sub-category",
     *     summary="Create Sub Category",
     *     tags={"Sub Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name","main_category_id"},
     *            @OA\Property(property="name"),
     *            @OA\Property(property="main_category_id"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Sub Category created Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'main_category_id' => 'required',
            ]);

            $data = $request->all();
            $subCategory = SubCategory::create($data);

            return response([
                'message'=> "Sub Category Created Successfully!",
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
     *     path="/api/sub-category/{id}",
     *     summary="Get Sub Category By ID",
     *     tags={"Sub Category"},
     *     @OA\Response(response="200", description="Get Sub Category successfully")
     * )
     */
    public function show($id)
    {
        $subCategory = SubCategory::with('maincategory')->find($id);
        if (!$subCategory) {
            return response()->json(['message' => 'Sub Category not found'], 404);
        }

        return response()->json($subCategory, 200);
    }

    // Update the specified main category
    /**
    *@OA\Patch(
     *     path="/api/sub-category/{id}",
     *     summary="Update Sub Category",
     *     tags={"Sub Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="is_active"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Sub Category Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'Sub Category not found'], 404);
        }
        
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'is_active' => 'boolean',
            ]);
            $data = $request->all();
            $subCategory->update($data);
            return response()->json($subCategory, 200);
        } catch (ValidationException $ex) {
            return response()->json($ex->validator->errors(), $ex->status);
        }
        
    }

    // Remove the specified main category from storage
    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['message' => 'Sub Category not found'], 404);
        }

        $subCategory->delete();

        return response()->json(['message' => 'Sub Category deleted successfully'], 200);
    }


}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class MainCategoryController extends Controller
{
    // List all main categories
    /**
    *@OA\Get(
     *     path="/api/main-category",
     *     summary="Get Main Category List",
     *     tags={"Main Category"},
     *     @OA\Response(response="200", description="Get Main Category successfully")
     * )
     */
    public function index()
    {
        return response()->json(MainCategory::all(), 200);
    }

    // Store a newly created main category
    /**
    *@OA\Post(
     *     path="/api/main-category",
     *     summary="Create Main Category",
     *     tags={"Main Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name"},
     *            @OA\Property(property="name"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Main Category created Successfully"),
     * )
    */

    
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:main_categories',
            ]);

            $data = $request->all();

            $mainCategory = MainCategory::create($data);
            return response([
                'message'=> "Main Category Created Successfully!",
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
     *     path="/api/main-category/{id}",
     *     summary="Get Main Category By ID",
     *     tags={"Main Category"},
     *     @OA\Response(response="200", description="Get Main Category successfully")
     * )
     */
    public function show($id)
    {
        $mainCategory = MainCategory::find($id);

        if (!$mainCategory) {
            return response()->json(['message' => 'Main Category not found'], 404);
        }

        return response()->json($mainCategory, 200);
    }

    // Update the specified main category
    /**
    *@OA\Patch(
     *     path="/api/main-category/{id}",
     *     summary="Update Main Category",
     *     tags={"Main Category"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="is_active"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Main Category Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $mainCategory = MainCategory::find($id);

        if (!$mainCategory) {
            return response()->json(['message' => 'Main Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();

        $mainCategory->update($data);

        return response([
            'message'=> "Main Category Updated Successfully!",
        ],201);
    }

    // Remove the specified main category from storage
    public function destroy($id)
    {
        $mainCategory = MainCategory::find($id);

        if (!$mainCategory) {
            return response()->json(['message' => 'Main Category not found'], 404);
        }

        $mainCategory->delete();

        return response()->json(['message' => 'Main Category deleted successfully'], 200);
    }
}

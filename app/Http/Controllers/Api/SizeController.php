<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Size;

class SizeController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/sizes",
     *     summary="Get Size List",
     *     tags={"Size"},
     *     @OA\Response(response="200", description="Get Sizes successfully")
     * )
     */
    public function index()
    {
        $sizes = Size::get();
        return response()->json($sizes, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
    *@OA\Post(
     *     path="/api/sizes",
     *     summary="Create Size",
     *     tags={"Size"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name"},
     *            @OA\Property(property="name"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Size created Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:sizes',
            ]);
            $data = $request->all();
            $size = Size::create($data);
            return response([
                'message'=> "Size Created Successfully!",
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }

    /**
    *@OA\Get(
     *     path="/api/sizes/{id}",
     *     summary="Get Size By ID",
     *     tags={"Size"},
     *     @OA\Response(response="200", description="Get Size successfully")
     * )
     */
    public function show($id)
    {
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['message' => ' Size not found'], 404);
        }

        return response()->json($size, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
    *@OA\Patch(
     *     path="/api/sizes/{id}",
     *     summary="Update Size",
     *     tags={"Size"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="is_active", type="boolean"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Size Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        try {
            $size = Size::find($id);

            if (!$size) {
                return response()->json(['message' => 'Size not found'], 404);
            }

            $request->validate([
                'name' => 'string',
                'is_active' => 'boolean',

            ]);

            $data = $request->only('name','is_active');
            $size->update($data);
            return response([
                'message'=> "Size Updated Successfully!",
            ],201);
        } catch (\Exception $e) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

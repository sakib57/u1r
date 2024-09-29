<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Color;

class ColorController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/colors",
     *     summary="Get Color List",
     *     tags={"Color"},
     *     @OA\Response(response="200", description="Get Colors successfully")
     * )
     */
    public function index()
    {
        $colors = Color::get();
        return response()->json($colors, 200);
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
     *     path="/api/colors",
     *     summary="Create Color",
     *     tags={"Color"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name"},
     *            @OA\Property(property="name"),
     *            @OA\Property(property="hex_code")
     *          )
     *       ),
     *     @OA\Response(response="200", description="Color created Successfully"),
     * )
    */
    public function store(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|unique:colors',
                'hex_code' => 'required|string',
            ]);
            $data = $request->all();
            $color = Color::create($data);
            return response([
                'message'=> "Color Created Successfully!",
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }

    /**
    *@OA\Get(
     *     path="/api/colors/{id}",
     *     summary="Get Color By ID",
     *     tags={"Color"},
     *     @OA\Response(response="200", description="Get Color successfully")
     * )
     */
    public function show($id)
    {
        $color = Color::find($id);

        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

        return response()->json($color, 200);
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
     *     path="/api/colors/{id}",
     *     summary="Update Color",
     *     tags={"Color"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="hex_code"),
     *            @OA\Property(property="is_active", type="boolean"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Color Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        try {
            $color = Color::find($id);

            if (!$color) {
                return response()->json(['message' => 'Color not found'], 404);
            }

            $request->validate([
                'name' => 'string',
                'hex_code' => 'string',
                'is_active' => 'boolean',

            ]);

            $data = $request->only('name','hex_code','is_active');
            $color->update($data);
            return response([
                'message'=> "Color Updated Successfully!",
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

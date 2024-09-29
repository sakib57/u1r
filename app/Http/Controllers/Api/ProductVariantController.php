<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
    *@OA\Get(
     *     path="/api/product-variant/{id}",
     *     summary="Get Product variant with id",
     *     tags={"Product Variant"},
     *     @OA\Response(response="200", description="Get Data Successfull")
     * )
     */
    public function show($id)
    {
        $productVariant = ProductVariant::where('id',$id)->first();
        return response()->json($productVariant, 200);
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
     *     path="/api/product-variant/{id}",
     *     summary="Update Product variant stock with id",
     *     tags={"Product Variant"},
     *       @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"stock"},
     *            @OA\Property(property="stock",type="integer"),
     *          )
     *       ),
     *     @OA\Response(response="201", description="Product Variant Stock Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $productVariant = ProductVariant::find($id);

        if (!$productVariant) {
            return response()->json(['message' => 'Product Variant not found'], 404);
        }

        $request->validate([
            'stock' => 'required|numeric',
        ]);

        $data = $request->only('stock');
        $productVariant->update($data);
        return response([
            'message'=> "Product Variant stock Updated Successfully!",
        ],201);
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

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItem;

class CartItemController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/cart-items",
     *     summary="Get Cart Item List",
     *     tags={"Cart Item"},
     *     @OA\Response(response="200", description="Get data successfully")
     * )
     */
    public function index()
    {
        $user = \Auth::user();
        $brands = CartItem::with('product','product.store','product.images','variant','variant.color','variant.size','variant.image')
        ->where('user_id',$user->id)->get();
        return response()->json($brands, 200);
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
     *     path="/api/cart-items",
     *     summary="Create Cart Item",
     *     tags={"Cart Item"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"product_id","quantity","sub_total"},
     *            @OA\Property(property="product_id",type="integer"),
     *            @OA\Property(property="product_variant_id",type="integer"),
     *            @OA\Property(property="quantity", type="integer"),
     *            @OA\Property(property="sub_total", type="integer"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Cart item added Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $user = \Auth::user();
            $request->validate([
                'product_id' => 'required|numeric',
                'product_variant_id' => 'numeric',
                'quantity' => 'numeric',
                'sub_total' => 'numeric',
            ]);
            $data = $request->all();
            $data["user_id"] = $user->id;
            $cartItem = CartItem::create($data);
            return response([
                'message'=> "Cart Item Created Successfully!",
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     *     path="/api/cart-items/{id}",
     *     summary="Update Cart Item",
     *     tags={"Cart Item"},
     *       @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"quantity"},
     *            @OA\Property(property="quantity",type="integer"),
     *          )
     *       ),
     *     @OA\Response(response="201", description="Cart Item Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['message' => 'Cart Item not found'], 404);
        }

        $request->validate([
            'quantity' => 'required|numeric',
        ]);

        $data = $request->only('quantity');
        $cartItem->update($data);
        return response([
            'message'=> "Cart Item Updated Successfully!",
        ],201);
    }

    
    /**
    *@OA\Delete(
     *     path="/api/cart-items/{id}",
     *     summary="Delete Cart Item",
     *     tags={"Cart Item"},
     *     @OA\Response(response="200", description="Cart Item Deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $ids = explode(",", $id);
        CartItem::find($ids)->each(function ($cartItem, $key) {
            $cartItem->delete();
        });

        
        // $cartItem = CartItem::find($id);
        // if (!$cartItem) {
        //     return response()->json(['message' => 'Cart Item not found'], 404);
        // }
        // $cartItem->delete();

        return response()->json(['message' => 'Cart Item deleted successfully'], 201);
    }
}

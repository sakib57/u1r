<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreOrder;

class StoreOrderController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/store-orders/{store_id}",
     *     summary="Get Store Order List",
     *     tags={"Store Order"},
     *     @OA\Response(response="200", description="Get Store Order successfully")
     * )
     */
    public function index($store_id)
    {
        $store_orders = StoreOrder::with(['store','order','orderitems'])->where('store_id',$store_id)->get();
        return response()->json($store_orders, 200);
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
     *     path="/api/store-orders/{id}",
     *     summary="Update Stor Order",
     *     tags={"Store Order"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"shipping_status"},
     *            @OA\Property(property="shipping_status"),
     *          ),
     *       ),
     *     @OA\Response(response="200", description="Store Order Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        
        try {
            $store_order = StoreOrder::find($id);
            if(!$store_order){
                return response([
                    'message'=> "Store Order not found!",
                ],404);
            }
            $validation = $request->validate([
                'shipping_status' => 'required|string',
            ]);
   
           $data = $request->only('shipping_status');
           $store_order->update($data);
           return response()->json($store_order, 200);
        } catch (QueryException $exception) {
            if ($exception->errorInfo[1] == 1062) {
                throw ValidationException::withMessages("Something Wrong");
            } else {
                throw $exception;
            }
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

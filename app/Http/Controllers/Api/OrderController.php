<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItems;
use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StoreOrder;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use DB;

class OrderController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/orders",
     *     summary="Get Order List",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="user id",
     *         @OA\Schema(type="number")
     *     ),
     *      @OA\Parameter(
     *         name="order_status",
     *         in="query",
     *         description="Order status",
     *         @OA\Schema(type="string",enum={"Pending","Confirmed"})
     *     ),
     *      @OA\Parameter(
     *         name="payment_status",
     *         in="query",
     *         description="Payment status",
     *         @OA\Schema(type="string",enum={"Unpaid","Paid","PartialPaid"})
     *     ),
     *      @OA\Parameter(
     *         name="shipping_status",
     *         in="query",
     *         description="Shipping status",
     *         @OA\Schema(type="string",enum={"Processing","OnTheWay","Delivered","Returned"})
     *     ),
     *     @OA\Response(response="201", description="Get Order List successfully")
     * )
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->has('user_id')){
            array_push($where, ['user_id','=',$request->input('user_id')]);
        }
    
        if(count($where) > 0){
            $orders = Order::where($where)->with([
                'storeorders','storeorders.store','storeorders.orderitems',
                'storeorders.orderitems.product',
                'storeorders.orderitems.product.images'])->get();
        }else{
            $orders = Order::with([
                'storeorders','storeorders.store','storeorders.orderitems',
                'storeorders.orderitems.product',
                'storeorders.orderitems.product.images'])->get();
        }

        return response()->json($orders, 200);

    }

    /**
    *@OA\Get(
     *     path="/api/orders-with-token",
     *     summary="Get Order List with token",
     *     tags={"Order"},
     *     @OA\Response(response="201", description="Get Order List with token successfully")
     * )
     */
    public function getOrdersWithToken(){
        $user = auth()->user();
        $orders = Order::where('user_id',$user->id)
        ->with(
            ['storeorders','storeorders.store',
            'storeorders.orderitems','storeorders.orderitems.product',
            'storeorders.orderitems.product.images'
            ])->get();
        return response()->json($orders, 200);
    }

    

    /**
    *@OA\Post(
     *     path="/api/orders",
     *     summary="Submit Order",
     *     tags={"Order"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"first_name","last_name","phone_no","city","shipping_address","payment_id"},
     *            @OA\Property(property="first_name"),
     *            @OA\Property(property="last_name"),
     *            @OA\Property(property="email"),
     *            @OA\Property(property="phone_no"),
     *            @OA\Property(property="district"),
     *            @OA\Property(property="city"),
     *            @OA\Property(property="postal_code"),
     *            @OA\Property(property="shipping_address"),
     *            @OA\Property(property="payment_id",type="number"),
     *            @OA\Property(
     *               property="stores", 
     *               type="array",
     *               @OA\Items(
     *                  @OA\Property(property="store_id",type="integer"),
     *                  @OA\Property(property="shipping_cost",type="integer"),
     *                  @OA\Property(property="discount_type"),
     *                  @OA\Property(property="discount",type="integer"),
     *                  @OA\Property(property="total",type="integer"),
     *                  @OA\Property(
     *                     property="items", 
     *                     type="array",
     *                     @OA\Items(
     *                       @OA\Property(property="product_id",type="integer"),
     *                       @OA\Property(property="product_variant_id",type="integer"),
     *                       @OA\Property(property="quantity",type="integer"),
     *                     ) 
     *                  )
     *               ) 
     *             )
     *          ),
     *       ),
     *     @OA\Response(response="200", description="Order submitted Successfully")
     * )
    */
    public function store(Request $request)
    {
        $user = auth()->user();
        try {
            $validation = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'string',
                'phone_no' => 'required|string',
                'postal_code' => 'required|string',
                'district' => 'required|string',
                'city' => 'required|string',
                'shipping_address' => 'required|string',
                'payment_id' => 'required',
                'order_status' => [Rule::enum(OrderStatus::class)],
                'shipping_status' => [Rule::enum(ShippingStatus::class)],
                'stores.*.store_id' => 'required|numeric',
                'stores.*.shipping_cost' => 'required|numeric',
            ]);

            $payment = Payment::where('id',$request->payment_id)->first();
            if(!$payment){
                return response([
                    'message'=> "Payment not found!",
                ],404);
            }

            $subtotal = 0;
            $data = $request->except('items');
            $data['user_id'] = $user->id;
            $tracking_no = strtoupper(Str::random(15));
            $is_in_db = Order::where('tracking_no',$tracking_no)->first();
            if(!$is_in_db){
                $data['tracking_no'] = $tracking_no;
            }else{
                $data['tracking_no'] = strtoupper(Str::random(15));
            }
            foreach($request->stores as $store){
                foreach($store['items'] as $item){
                    $product = Product::find($item['product_id']);
                    if(!$product){
                        return response([
                            'message'=> "Product not found!",
                        ],404);
                        break;
                    }
                    if($product->is_active){
                        $subtotal += ($product->price * (int)$item['quantity']);
                    }
                }
            }
            
            $data['sub_total'] = $subtotal;
            $data['grand_total'] = $subtotal + (int)$request->shipping_cost;
            DB::beginTransaction();
            $order = Order::create($data);
            foreach($request->stores as $store){
                $store_order_data = [
                    'order_id' => $order->id,
                    'store_id' => $store['store_id'],
                    'shipping_cost' => $store['shipping_cost'],
                    'discount_type' => array_key_exists("discount_type",$store) ? $store['discount_type'] : "",
                    'discount' => array_key_exists("discount",$store) ? $store['discount'] : 0,
                    'total' => $store['total'],
                ];
                $store_order = StoreOrder::create($store_order_data);

                foreach($store['items'] as $item){
                    $order_item_data = [
                        "order_id" => $order->id,
                        "store_order_id" => $store_order->id,
                        "product_id" => $item['product_id'],
                        "product_variant_id" => $item['product_variant_id'],
                        "quantity" => $item['quantity']
                    ];
                    OrderItems::create($order_item_data);
                }
                
            }
            DB::commit();
            return response([
                'message'=> "Order Placed Successfully!",
            ],201);
        }catch (ValidationException $ex) {
            DB::rollback();
            return response([
                'message'=> $ex->validator->errors(),
            ],$ex->status);
        }catch (QueryException $ex) {
            DB::rollback();
            return response([
                'message'=> $ex,
            ],$ex->status);
        }
    }

    
//     /**
//     *@OA\Get(
//      *     path="/api/orders/{id}",
//      *     summary="Get Order by id",
//      *     tags={"Order"},
//      *     @OA\Parameter(
//      *         name="id",
//      *         in="query",
//      *         description="order id",
//      *         @OA\Schema(type="number")
//      *     ),
//      *     @OA\Response(response="200", description="Get order Successfully")
//      * )
//     */
//     public function show($id)
//     {
//         $order = Order::with(['orderItems','user','orderItems.product'])->get()->find($id);
//         return response()->json($order, 200);
//     }

//     /**
//      * Show the form for editing the specified resource.
//      *
//      * @param  int  $id
//      * @return \Illuminate\Http\Response
//      */
//     public function edit($id)
//     {
//         //
//     }

    /**
    *@OA\Patch(
     *     path="/api/orders/{id}",
     *     summary="Update Order Status",
     *     tags={"Order"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="payment_status"),
     *          ),
     *       ),
     *     @OA\Response(response="200", description="Order updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $order = Order::where('id',$id)->with('storeorders.store','payment')->first();
        if(!$order){
            return response([
                'message'=> "Order not found!",
            ],404);
        }

        // return response([
        //     'order'=> $order,
        // ],200);

        try {
            DB::beginTransaction();
            if($request->payment_status){
                // wallet update
                if($order->payment->status == "Unpaid" && $request->payment_status == "Paid"){
                    foreach ($order->storeorders as $storeOrder) {
                        
                        $user_id = $storeOrder->store->user_id;
                        $user = User::find($user_id);
                        $amount = (int)$storeOrder->total - (((int)$storeOrder->total * 10)/100);
                        $new_amount = $user->balance + $amount;
                        
                        $data = ["balance"=> 2346236];
                        // return response([
                        //     'user'=> $user,
                        // ],400);
                        $user->update($data);
                    }
                }
                
            }else{
                return response([
                    'message'=> "No status provided!",
                ],400);
            }
            $data = ["status" => $request->payment_status];
            $payment = Payment::where('id',$order->payment_id)->first();
            $payment->update($data);
            DB::commit();
            return response([
                'message'=> "Payment Status Updated!",
            ],200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response([
                'error'=> $th,
            ],200);
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

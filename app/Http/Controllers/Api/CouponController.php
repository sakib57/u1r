<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/coupons/{store_id}",
     *     summary="Get Coupon List with store id",
     *     tags={"Coupon"},
     *     @OA\Response(response="200", description="Get Coupons successfully")
     * )
     */
    public function index($store_id)
    {
        $coupons = Coupon::where('store_id', $store_id)->get();
        return response()->json($coupons, 200);
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
     *     path="/api/coupons",
     *     summary="Create Coupon",
     *     tags={"Coupon"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"store_id","name","code","amount","expire_date"},
     *            @OA\Property(property="store_id",type="integer"),
     *            @OA\Property(property="name"),
     *            @OA\Property(property="code"),
     *            @OA\Property(property="amount",type="integer"),
     *            @OA\Property(property="expire_date",type="date")
     *          )
     *       ),
     *     @OA\Response(response="200", description="Coupon created Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'store_id' => 'required',
                'name' => 'required|string|unique:coupons',
                'code' => 'required|string',
                'amount' => 'required|integer',
                'expire_date' => 'required|date',
            ]);
            $data = $request->all();
            $coupon = Coupon::create($data);
            return response([
                'message'=> "Coupon Created Successfully!",
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }


    /**
    *@OA\Post(
     *     path="/api/coupons/check",
     *     summary="Check Coupon",
     *     tags={"Coupon"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"store_id","code"},
     *            @OA\Property(property="store_id",type="integer"),
     *            @OA\Property(property="code"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Coupon checked Successfully"),
     * )
    */
    public function check(Request $request)
    {
        try {
            $request->validate([
                'store_id' => 'required|integer',
                'code' => 'required|string',
            ]);
            $coupon =  Coupon::where('store_id', $request->store_id)
            ->where('code', $request->code)->first();
            return response([
                'coupon'=> $coupon,
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

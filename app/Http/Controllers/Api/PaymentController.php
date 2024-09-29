<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Validation\Rule;
use App\Enums\PaymentStatus;

class PaymentController extends Controller
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
    *@OA\Post(
     *     path="/api/payments",
     *     summary="Create Payment",
     *     tags={"Payment"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="type"),
     *            @OA\Property(property="transection_no"),
     *            @OA\Property(property="amount",type="integer"),
     *            @OA\Property(property="status")
     *          )
     *       ),
     *     @OA\Response(response="200", description="Payment created Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $user = \Auth::user();
            $request->validate([
                'type' => 'required|string',
                'transection_no' => 'string',
                'amount' => 'required|integer',
            ]);
            $data = $request->all();
            $data["user_id"] = $user->id;
            $payment = Payment::create($data);
            return response([
                'message'=> "Payment Created Successfully!",
                'payment' => $payment
            ],201);
        } catch (\Exception $exception) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }


    /**
    *@OA\Get(
     *     path="/api/payments/{id}",
     *     summary="Get Payment By ID",
     *     tags={"Payment"},
     *     @OA\Response(response="200", description="Get Payment successfully")
     * )
     */
    public function show($id){
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment, 200);
    }

    public function edit($id){
        //
    }


    /**
    *@OA\Patch(
     *     path="/api/payments/{id}",
     *     summary="Update Payment",
     *     tags={"Payment"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="status"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Payment Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $request->validate([
                'status' => [Rule::enum(PaymentStatus::class)],
            ]);

            $data = $request->only('amount');
            $payment->update($data);
            return response([
                'message'=> "Payment Updated Successfully!",
            ],201);
        } catch (\Exception $e) {
            return response([
                'message'=> $exception,
            ],500);
        }
    }


    public function destroy($id)
    {
        //
    }
}

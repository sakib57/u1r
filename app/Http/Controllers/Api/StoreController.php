<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\StoreCategory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use DB;

class StoreController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/stores",
     *     summary="Get Store List",
     *     tags={"Store"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="user id",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Response(response="200", description="Get Store List successfully")
     * )
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->has('user_id')){
            array_push($where, ['user_id','=',$request->input('user_id')]);
        }
        if(count($where) > 0){
            $orders = Store::where($where)->get();
        }else{
            $orders = Store::get();
        }

        return response()->json($orders, 200);

    }

    /**
    *@OA\Get(
     *     path="/api/stores-by-token",
     *     summary="Get Store List by token",
     *     tags={"Store"},
     *     @OA\Response(response="200", description="Get Store List by token successfully")
     * )
     */


    public function storesByToken(Request $request)
    {
        $user = auth()->user();
        $orders = Store::where('user_id',$user->id)->get();
        return response()->json($orders, 200);
    }

    

    /**
    *@OA\Post(
     *     path="/api/stores",
     *     summary="Create Store",
     *     tags={"Store"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"name","city","address"},
     *            @OA\Property(property="name"),
     *            @OA\Property(property="email"),
     *            @OA\Property(property="phone_no"),
     *            @OA\Property(property="city"),
     *            @OA\Property(property="postal_code"),
     *            @OA\Property(property="address"),
     *            @OA\Property(
     *              property="main_categories", 
     *                 type="array",
    *                    @OA\Items(
    *                       @OA\Property(property="id"),
    *                    )  
     *              )
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
                'name' => 'required|string',
                'email' => 'string',
                'phone_no' => 'string',
                'postal_code' => 'string',
                'city' => 'required|string',
                'address' => 'required|string',
                'main_categories.*.id' => 'required',
            ]);

            $data = $request->except('main_categories');
            $data['user_id'] = $user->id;
            DB::beginTransaction();
            $store = Store::create($data);
            foreach($request->main_categories as $main_category){
                $store_main_category = [
                    "store_id" => $store->id,
                    "main_category_id" => $main_category['id']
                ];
                StoreCategory::create($store_main_category);
            }
            DB::commit();
            return response([
                'message'=> "Store Created Successfully!",
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

    
    /**
    *@OA\Get(
     *     path="/api/stores/{id}",
     *     summary="Get Store by id",
     *     tags={"Store"},
     *     @OA\Response(response="200", description="Get Store Successfully")
     * )
    */
    public function show($id)
    {
        $order = Store::with(['storeCategories','user','storeCategories.mainCategory'])->get()->find($id);
        return response()->json($order, 200);
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
     *     path="/api/stores/{id}",
     *     summary="Update Store",
     *     tags={"Store"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *            @OA\Property(property="email"),
     *            @OA\Property(property="phone_no"),
     *            @OA\Property(property="city"),
     *            @OA\Property(property="postal_code"),
     *            @OA\Property(property="address"),
     *            @OA\Property(
     *              property="main_categories", 
     *                 type="array",
     *                    @OA\Items(
     *                       @OA\Property(property="id"),
     *                    )  
     *             )
     *           ),
     *       ),
     *     @OA\Response(response="200", description="Store Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $store = Store::find($id);
        if(!$store){
            return response([
                'message'=> "Store not found!",
            ],404);
        }
        try {
            $validation = $request->validate([
                'name' => 'required|string',
                'email' => 'string',
                'phone_no' => 'string',
                'postal_code' => 'string',
                'city' => 'required|string',
                'address' => 'required|string',
                'main_categories.*.id' => 'required',
            ]);
   
           $data = $request->all();
           DB::beginTransaction();
           StoreCategory::where('store_id',$store->id)->delete();
           $store->update($data);

           // Create product variations
            if(count($request->main_categories) > 0){
                foreach($request->main_categories as $main_category){
                    $store_main_category = [
                        "store_id" => $store->id,
                        "main_category_id" => $main_category['id']
                    ];
                    StoreCategory::create($store_main_category);
                }
            }
            
           DB::commit();
           return response()->json($store, 200);
        } catch (QueryException $exception) {
            DB::rollback();
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


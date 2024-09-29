<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UtilityRequest;

class UtilityRequestController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/utility-requests",
     *     summary="Get Utility Request List",
     *     tags={"Utility Request"},
     *      @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status",
     *     ),
     *     @OA\Response(response="200", description="Get Utility Request successfully")
     * )
     */
    public function index(Request $request)
    {
        $where = [];
        if($request->status){
            $where['status'] = $request->status;
        }
        $subCategories = UtilityRequest::where($where)->with('user')->get();
        return response()->json($subCategories, 200);
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
     *     path="/api/utility-requests",
     *     summary="Create Utility Request",
     *     tags={"Utility Request"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"type","name"},
     *            @OA\Property(property="type"),
     *            @OA\Property(property="name"),
     *          )
     *       ),
     *     @OA\Response(response="200", description="Utility Request created Successfully"),
     * )
    */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'name' => 'required|string',
            ]);

            $data = $request->all();
            $user = auth()->user();
            $data['user_id'] = $user->id;
            $utilityRequest = UtilityRequest::create($data);

            return response([
                'message'=> "Utility Request Created Successfully!",
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
     *     path="/api/utility-requests/{id}",
     *     summary="Update Utility Request",
     *     tags={"Utility Request"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            @OA\Property(property="name"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Utility Request Updated Successfully")
     * )
    */
    public function update(Request $request, $id)
    {
        $utilityRequest = UtilityRequest::find($id);

        if (!$utilityRequest) {
            return response()->json(['message' => 'Utility Request not found'], 404);
        }
        
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            $data = $request->only('name');
            $utilityRequest->update($data);
            return response()->json($utilityRequest, 200);
        } catch (ValidationException $ex) {
            return response()->json($ex->validator->errors(), $ex->status);
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

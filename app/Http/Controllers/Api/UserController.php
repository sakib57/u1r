<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


class UserController extends Controller
{
    /**
    *@OA\Get(
     *     path="/api/users",
     *     summary="Get all user list",
     *     tags={"User"},
     *         @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="user role",
     *         @OA\Schema(type="string",enum={"user","seller","admin"})
     *     ),
     *     @OA\Response(response="200", description="Get all user info Successfully")
     * )
    */
    public function index(Request $request){
        $where = [];

        if($request->has('role')){
            array_push($where, ['role','=',$request->input('role')]);
        }
        if(count($where) > 0){
            $users = User::where($where)->with(['stores'])->get();
        }else{
            $users = User::with(['stores'])->get();
        }
        // $users = User::all();
        return response()->json($users);
    }



    /**
    *@OA\Get(
     *     path="/api/profile",
     *     summary="Get authenticated user",
     *     tags={"User"},
     *     security={"bearerAuth":{}},
     *     @OA\Response(response="200", description="Get user info Successfully")
     * )
    */
    public function profile(){

        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Users profile data",
            "data" => $userdata
        ]);
    }


    /**
    *@OA\Patch(
     *     path="/api/profile",
     *     summary="Update User",
     *     tags={"User"},
     *      @OA\RequestBody(
     *         @OA\JsonContent(
     *            required={"role"},
     *            @OA\Property(property="first_name"),
     *            @OA\Property(property="last_name"),
     *            @OA\Property(property="image"),
     *            @OA\Property(property="district"),
     *            @OA\Property(property="city"),
     *            @OA\Property(property="postal_code"),
     *            @OA\Property(property="address"),
     *            @OA\Property(property="phone_no"),
     *            @OA\Property(property="role"),
     *            @OA\Property(property="is_active",type="boolean"),
     *          ),
     *       ),
     *     @OA\Response(response="201", description="Sub Category Updated Successfully")
     * )
    */
    public function updateProfile(Request $request){

        $userdata = auth()->user();
        $user = User::find($userdata->id);

        if(!$user){
            return response()->json([
                "Message" => "User not found!"
            ]);
        }
        try {
            $validation = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'string',
                'image' => 'string',
                'district' => 'string',
                'city' => 'string',
                'postal_code' => 'string',
                'address' => 'string',
                'phone_no' => 'string',
                'role' => 'string',
                'is_active' => 'boolean',
            ]);

            $data = $request->only(
                'first_name','last_name',
                'image','district','city',
                'address','postal_code',
                'phone_no','role','is_active',
            );
            $user->update($data);
            return response()->json([
                "message" => "User updated successfully!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "exception" => $e
            ]);
        } 
    }



    /**
    *@OA\Post(
     *     path="/api/forgot-password",
     *     summary="Retrive forgotten password",
     *     tags={"User"},
     *      @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"email"},
    *            @OA\Property(property="email"),
    *          ),
    *       ),
     *     @OA\Response(response="200", description="Mail send Successfully")
     * )
     */
    public function forgotPassword(Request $request){
        $request->validate([
            "email" => "required|email",
        ]);
        $user = User::where('email', $request->email)->first();
        if(!$user){
            return response()->json([
                "status" => false,
                "message" => "This email is not registered in the system!"
            ]);
        }

        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return response([
                'message'=> 'Password reset link send to your email address!'
            ]);
        }
        return response([
            'message'=> 'Could not send email'
        ],400);
    }

    /**
    *@OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset new password",
     *     tags={"User"},
     *      @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"email","token", "password", "password_confirmation"},
    *            @OA\Property(property="email"),
    *            @OA\Property(property="token"),
    *            @OA\Property(property="password"),
    *            @OA\Property(property="password_confirmation")
    *          ),
    *       ),
     *     @OA\Response(response="200", description="Password changed Successfully")
     * )
     */
    public function resetPassword(Request $request){
        try {
            $request->validate([
                "token" => "required",
                'email' => 'required|email',
                "password" => "required|confirmed"
            ]);
            $status = Password::reset(
                $request->only('email','password','password_confirmation','token'),
                function($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();
                    $user->tokens()->delete();
                }
            );
            if ($status == Password::PASSWORD_RESET) {
                return response([
                    'message'=> 'Password reset successfully'
                ]);
            }
    
            return response([
                'message'=> 'Could not reset password.',
                'error'=> $status,
                'should'=> Password::PASSWORD_RESE
            ], 400);
        } catch (\Exception $e) {
            return response([
                'message'=> 'Could not reset password2.',
                'error'=> $e
            ], 400);
        }
        
    }

    public function update(Request $request, $id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'frist_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'full_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_no' => 'sometimes|required|string|max:15|unique:users,phone_no,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'user_role_id' => 'sometimes|required|integer|exists:user_roles,id',
            'checkbox_message' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ], 400);
        }

        $user->update([
            'frist_name' => $request->get('frist_name', $user->frist_name),
            'last_name' => $request->get('last_name', $user->last_name),
            'full_name' => $request->get('full_name', $user->full_name),
            'email' => $request->get('email', $user->email),
            'phone_no' => $request->get('phone_no', $user->phone_no),
            'user_role_id' => $request->get('user_role_id', $user->user_role_id),
            'checkbox_message' => $request->get('checkbox_message', $user->checkbox_message),
            'password' => $request->has('password') ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully',
            'updated_user' => $user->load('role') // Eager loading the role relationship
        ], 200);
    }


    public function destroy($id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['status' => 200, 'message' => 'User deleted successfully'], 200);
    }


}

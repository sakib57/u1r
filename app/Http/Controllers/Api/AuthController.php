<?php

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\MailController;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
    *@OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *    @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"first_name","last_name","email", "password", "password_confirmation"},
    *            @OA\Property(property="first_name"),
    *            @OA\Property(property="last_name"),
    *            @OA\Property(property="email"),
    *            @OA\Property(property="role"),
    *            @OA\Property(property="password"),
    *            @OA\Property(property="password_confirmation")
    *          ),
    *    ),
     *     @OA\Response(response="201", description="User registered successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
     public function register(Request $request){
        try {
            $request->validate([
                "first_name" => "required",
                "last_name" => "required",
                "email" => "required|email|unique:users",
                "role" => "string",
                "password" => "required|confirmed"
            ]);
            $user = User::create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "role" => $request->role ? $request->role : "user",
                "email" => $request->email,
                "phone_no" => $request->phone_no,
                "password" => Hash::make($request->password)
            ]);

            return response()->json([
                "user" => [
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "role" => $user->role,
                    "email" => $user->email,
                    "phone_no" => $request->phone_no,
                ],
                "message" => "User registered successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "error" => $th,
                "message" => "Something went wrong!"
            ],500);
        }
    }


    /**
    *@OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     tags={"Auth"},
     *    @OA\RequestBody(
    *         @OA\JsonContent(
    *            required={"email", "password"},
    *            @OA\Property(property="email"),
    *            @OA\Property(property="password"),
    *          ),
    *    ),
     *     @OA\Response(response="201", description="User logged in successfully"),
     * )
     */
    public function login(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);
        if(!empty($token)){
            $user = \Auth::user();
            return response()->json([
                "status" => true,
                "message" => "User logged in succcessfully",
                "role" => $user->role,
                "token" => $token
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid details"
        ]);
    }



    public function refreshToken(){
        $newToken = auth()->refresh();
        return response()->json([
            "status" => true,
            "message" => "New access token",
            "token" => $newToken
        ]);
    }

    /**
    *@OA\Post(
     *     path="/api/logout",
     *     summary="Logout a user",
     *     tags={"Auth"},
     *     @OA\Response(response="201", description="User logged out successfully")
     * )
     */
    public function logout(){
        auth()->logout();
        return response()->json([
            "status" => true,
            "message" => "User logged out successfully"
        ]);
    }


    
}
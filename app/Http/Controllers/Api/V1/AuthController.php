<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    "status" => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            return response()->json([
                "status" => true,
                "message" => "User registered successfully",
                "token" => $user->createToken("API TOKEN")->plainTextToken,
            ],Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => __("Failed to register"),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);
    
            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if(! Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            return response()->json([
                "status" => true,
                "message" => "User Logged In Successfully",
                "token" => auth()->user()->createToken('API TOKEN')->plainTextToken,
            ],Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => __("Failed to register"),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User Logged out Successfully",
        ],Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiController extends Controller
{
    //Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'error' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User Registed Successfully',
            'data' => $user
        ], 201);
    }

    //Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => "Don't have user information or Email/Password invalid"
            ], 401);
        }
        $user->tokens()->delete();
        //$accessToken = $user->createToken('access_token', ['access-api'])->plainTextToken;
        //$refreshToken = $user->createToken('refresh_token', ['refresh-api'])->plainTextToken;

        $accessToken = $user->createToken('access_token', ['access-api']);
        $accessToken->accessToken->expires_at = Carbon::now()->addMinutes(15);
        $accessToken->accessToken->save();

        $refreshToken = $user->createToken('refresh_token', ['refresh-api']);
        $refreshToken->accessToken->expires_at = Carbon::now()->addDays(7);
        $refreshToken->accessToken->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Login Successfully',
            'access_token' => $accessToken->plainTextToken,
            'refresh_token'=> $refreshToken->plainTextToken,
        ], 200);
    }

    //Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Successfully',
        ], 200);
        
    }

    //Refresh Token
    public function refresh(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if (!$token->can('refresh-api')) {
            return response()->json([
                'message' => 'Invalid Token Type',
            ], 403);
        }

        if ($token->expires_at && Carbon::parse($token->expires_at)->isPast()) {
            return response()->json([
                'message' => 'Refresh Token Expired',
            ], 401);
        }

        $user = $request->user();
        $token->delete();
        //$newAccessToken = $user->createToken('access_token')->plainTextToken;
        //$newRefreshToken = $user->createToken('refresh_token')->plainTextToken;
        $newAccessToken = $user->createToken('access', ['access-api']);
        $newAccessToken->accessToken->expires_at = now()->addMinute(15);
        $newAccessToken->accessToken->save();

        $newRefreshToken = $user->createToken('refresh', ['refresh-api']);
        $newRefreshToken->accessToken->expires_at = now()->addDay(7);
        $newRefreshToken->accessToken->save();

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ]);
    } 

    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ], 200);
    }
}

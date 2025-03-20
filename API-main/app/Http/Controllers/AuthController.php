<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Đăng nhập và trả về JWT token
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if ($token = JWTAuth::attempt($credentials)) {
                Log::info('Login successful for email ' . $request->email);
                return response()->json(['token' => $token]);
            } else {
                Log::error('Login failed: Unauthorized for email ' . $request->email);
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            Log::error('JWT token creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    // Lấy thông tin người dùng từ JWT token
    public function me()
    {
        return response()->json(auth()->user());
    }

    // Đăng xuất và hủy token
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }
}

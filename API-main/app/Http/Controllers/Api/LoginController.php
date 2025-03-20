<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class LoginController extends Controller
{
    public function check(Request $request) {
        // Kiểm tra dữ liệu đầu vào
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // Kiểm tra user có tồn tại không
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::error('Login failed: Email does not exist');
            return response()->json([
                'status' => false,
                'message' => 'Email không tồn tại'
            ], 404);
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($request->password, $user->password)) {
            Log::error('Login failed: Incorrect password for email ' . $request->email);
            return response()->json([
                'status' => false,
                'message' => 'Mật khẩu không đúng'
            ], 401);
        }

        // Log the user's role
        Log::info('User role: ' . $user->role);

        // Tạo JWT token
        try {
            $payload = [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'exp' => time() + (60 * 60) // Token hết hạn sau 1 giờ
            ];
            
            $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        } catch (\Exception $e) {
            Log::error('JWT token creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Token creation failed'
            ], 500);
        }

        Log::info('Login successful for email ' . $request->email);

        return response()->json([
            'status' => true,
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
            'role' => $user->role // Trả về role để frontend xử lý
        ], 200);
    }
}

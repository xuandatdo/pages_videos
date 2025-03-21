<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    public function register(Request $request) {
        // Xác thực dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    if (!str_ends_with($value, '@gmail.com')) {
                        $fail('The email must be a Gmail address (@gmail.com).');
                    }
                },
            ],
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required', // Đảm bảo Laravel nhận đúng giá trị
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 400);
        }

        // Tạo tài khoản người dùng
        User::create([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'name' => $request->firstName . ' ' . $request->lastName, // Thêm trường name
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user' // Đặt mặc định là user
        ]);

        return response()->json([
            'status' => true,
            'message' => "User Registration successful",
        ], 201);
    }
}

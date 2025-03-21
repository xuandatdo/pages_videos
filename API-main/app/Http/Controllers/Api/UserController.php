<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Lấy danh sách người dùng
    public function getUsers()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users'], 500);
        }
    }

    // Thêm người dùng mới
    public function store(Request $request)
    {
        // Validate dữ liệu nhập vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,user',
        ], [
            'email.regex' => 'The email format is invalid. Please use a valid email like test@gmail.com.'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Băm mật khẩu trước khi lưu
        $hashedPassword = Hash::make($request->password);
        
        // Tạo user mới với mật khẩu đã mã hóa
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $hashedPassword,
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Added New User Successfully',
            'user' => $user
        ], 201);

        // $user = User::addUser($request->name, $request->email, $request->role, $request->password);
        // return response()->json($user, 201);
    }

    // Cập nhật thông tin người dùng
    public function update(Request $request, $id)
    {
        // Validate dữ liệu nhập vào
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($id),
            function ($attribute, $value, $fail) {
                if (!str_ends_with($value, '@gmail.com')) {
                    $fail('The email must be a Gmail address (@gmail.com).');
                }
            },
        ],
            'role' => 'required|string|in:admin,user',
            'password' => 'nullable|min:6', // Cho phép cập nhật mật khẩu, nhưng không bắt buộc
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            
            // Tìm user cần cập nhật
            $user = User::findOrFail($id);

            // Cập nhật thông tin
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
    
            $user->save();

            \Log::info('Updating user', ['id' => $id, 'data' => $request->all()]);
            $user = User::updateUser($id, $request->name, $request->email, $request->role);
            \Log::info('User updated successfully', ['user' => $user]);
            return response()->json($user);
        } catch (\Exception $e) {
            \Log::error('Failed to update user', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }
    // Xóa người dùng
    public function destroy($id)
        {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'User deleted successfully'
            ], 200);
        }
}
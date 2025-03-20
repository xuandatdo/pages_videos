<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ContactController extends Controller {

    // Gửi liên hệ
    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'number'  => 'nullable|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        Contact::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'number'  => $request->number,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json(['message' => 'Liên hệ của bạn đã được gửi!'], 201);
    }

    // Lấy danh sách liên hệ (chỉ dành cho admin)
    public function index(Request $request)
    {
        // // Kiểm tra xem người dùng đã đăng nhập chưa
        // $user = auth()->user();
        // if (!$user) {
        //     return response()->json(['message' => 'Người dùng chưa được xác thực!'], 401);
        // }

        // // Kiểm tra xem người dùng có phải là admin không
        // if ($user->role !== 'admin') {
        //     return response()->json(['message' => 'Bạn không có quyền truy cập!'], 403);
        // }

        // // Truy vấn danh sách liên hệ từ MySQL
        // $contacts = Contact::orderBy('created_at', 'desc')->get();
        // return response()->json($contacts, 200);

        return response()->json(Contact::all());
    }

    /// Xóa liên hệ
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json(['message' => 'Xóa liên hệ thành công!'], 200);
    }

    // Đánh dấu là đã đọc
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => true]);

        return response()->json(['message' => 'Đã đánh dấu là đã đọc'], 200);
    }
}

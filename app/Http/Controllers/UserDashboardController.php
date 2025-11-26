<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\OTPMail;
use App\Models\User;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('event')->latest()->get();
        return view('user.dashboard', compact('user', 'orders'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
                Storage::delete('public/' . $user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => 'storage/' . $path]);
        }

        return back()->with('success', 'Cập nhật ảnh đại diện thành công!');
    }

     public function sendOtp()
    {
        $user = Auth::user();
        
        // Tạo mã ngẫu nhiên 6 số
        $otp = rand(100000, 999999);
        
        // Lưu vào Cache trong 5 phút (300 giây) với key là "otp_ID_USER"
        Cache::put('otp_' . $user->id, $otp, 300);
        
        // Gửi mail
        try {
            Mail::to($user->email)->send(new OTPMail($otp));
            return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi đến email của bạn!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi gửi mail: ' . $e->getMessage()], 500);
        }
    }

    // 2. Hàm đổi mật khẩu (Sửa lại để check OTP)
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'otp' => 'required|numeric',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }
        
        // Lấy OTP từ Cache
        $cachedOtp = Cache::get('otp_' . $user->id);

        // Kiểm tra OTP
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
        }

        // Cập nhật mật khẩu
        $user->update([
            'password_hash' => Hash::make($request->new_password),
        ]);

        // Xóa OTP sau khi dùng xong
        Cache::forget('otp_' . $user->id);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function requestOrganizer()
    {
        $user = Auth::user();
        
        if ($user->role === 'organizer') {
            return back()->with('info', 'Bạn đã là Organizer rồi.');
        }

        if ($user->organizer_request_status === 'pending') {
            return back()->with('info', 'Yêu cầu của bạn đang chờ duyệt.');
        }

        $user->update([
            'organizer_request_status' => 'pending',
            'organizer_request_at' => now(),
        ]);

        return back()->with('success', 'Đã gửi yêu cầu nâng cấp tài khoản. Vui lòng chờ Admin duyệt.');
    }
}

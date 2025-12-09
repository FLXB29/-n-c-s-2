<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use App\Mail\OTPMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    // ============================================
    // ĐĂNG KÝ THÔNG THƯỜNG
    // ============================================

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'accepted',
        ], [
            'email.unique' => 'Email này đã được đăng ký.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'terms.accepted' => 'Bạn phải đồng ý với điều khoản.',
        ]);

        $registerData = [
            'full_name' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active'
        ];

        Session::put('register_data', $registerData);

        $otp = rand(100000, 999999);
        Cache::put('verify_email_otp_' . $request->email, $otp, 300);

        // $user = User::create([
        //     'full_name' => $request->fullName,
        //     'email' => $request->email,
        //     'phone' => $request->phone,
        //     'password_hash' => Hash::make($request->password),
        //     'role' => 'user',
        //     'status' => 'active'
        // ]);

        // Auth::login($user);

        // return $this->redirectBasedOnRole($user, 'Đăng ký thành công!');

        try {
            // Truyền OTP và Tiêu đề tùy chỉnh cho việc Đăng ký
            Mail::to($request->email)->send(new OTPMail($otp));
        } catch (\Exception $e) {
            // Xử lý nếu gửi mail lỗi (tùy chọn)
            return back()->withErrors(['email' => 'Không thể gửi email xác thực. Vui lòng thử lại.']);
        }

        // 5. Chuyển hướng
        return redirect()->route('register.verify');
    }

    // Hàm hiển thị form nhập OTP
    public function showVerifyForm()
    {
        // Nếu không có dữ liệu đăng ký trong session, đá về trang đăng ký
        if (!Session::has('register_data')) {
            return redirect()->route('register');
        }

        $email = Session::get('register_data')['email'];
        return view('auth.verify-email', compact('email'));
    }

    // Hàm xử lý xác thực OTP và tạo User
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $data = Session::get('register_data');
        if (!$data) return redirect()->route('register');

        // Lấy OTP từ Cache
        $cachedOtp = Cache::get('verify_email_otp_' . $data['email']);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
        }

        // === TẠO USER CHÍNH THỨC ===
        $user = User::create([
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
            'password_hash' => $data['password_hash'], // Lưu ý: password đã hash ở bước 1
            'role'      => 'user',
            'status'    => 'active',
            'email_verified_at' => now(), // Đánh dấu đã xác thực email
        ]);

        // Đăng nhập luôn
        Auth::login($user);

        // Xóa session và cache
        Session::forget('register_data');
        Cache::forget('verify_email_otp_' . $data['email']);

        return $this->redirectBasedOnRole($user, 'Đăng ký và xác thực thành công!');
    }

    // ============================================
    // ĐĂNG NHẬP THÔNG THƯỜNG
    // ============================================

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Kiểm tra tài khoản có bị khóa không
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa.',
                ]);
            }

            return $this->redirectBasedOnRole($user, 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    // ============================================
    // ĐĂNG NHẬP BẰNG GOOGLE
    // ============================================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Sử dụng stateless() để tránh lỗi InvalidStateException
            $googleUser = Socialite::driver('google')->stateless()->user();

            return $this->handleSocialLogin($googleUser, 'google');
        } catch (\Exception $e) {
            // Log lỗi để debug
            Log::error('Google login error: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }

    // ============================================
    // ĐĂNG NHẬP BẰNG FACEBOOK
    // ============================================

    public function redirectToFacebook()
    {
        // Chỉ request scope public_profile (không cần email vì app chưa được review)
        return Socialite::driver('facebook')
            ->scopes(['public_profile'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            // Sử dụng stateless() để tránh lỗi InvalidStateException
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            return $this->handleSocialLogin($facebookUser, 'facebook');
        } catch (\Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Facebook thất bại: ' . $e->getMessage());
        }
    }

    // ============================================
    // XỬ LÝ SOCIAL LOGIN CHUNG
    // ============================================

    protected function handleSocialLogin($socialUser, $provider)
    {
        $email = $socialUser->getEmail();
        $providerId = $socialUser->getId();

        // Nếu không có email (Facebook không cấp), tạo email giả từ provider ID
        if (!$email) {
            $email = $provider . '_' . $providerId . '@social.local';
        }

        // Bước 1: Tìm user theo provider ID trước
        $user = User::where($provider . '_id', $providerId)->first();

        // Bước 2: Nếu không tìm thấy, tìm theo email
        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            // User đã tồn tại - cập nhật thông tin social
            $user->update([
                $provider . '_id' => $providerId,
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        } else {
            // Tạo user mới
            $user = User::create([
                'full_name' => $socialUser->getName(),
                'email' => $email,
                'phone' => null,
                'password_hash' => Hash::make(Str::random(24)), // Random password
                $provider . '_id' => $providerId,
                'avatar' => $socialUser->getAvatar(),
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        // Kiểm tra tài khoản có bị khóa không
        if ($user->status !== 'active') {
            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        // Đăng nhập user
        Auth::login($user, true);

        return $this->redirectBasedOnRole($user, 'Đăng nhập thành công!');
    }

    // ============================================
    // ĐĂNG XUẤT
    // ============================================

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Đăng xuất thành công!');
    }

    // ============================================
    // HELPER: REDIRECT THEO ROLE
    // ============================================

    protected function redirectBasedOnRole($user, $message = null)
    {
        $redirect = match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'organizer' => redirect()->route('organizer.dashboard'),
            default => redirect()->route('home'),
        };

        return $message ? $redirect->with('success', $message) : $redirect;
    }

    // ============================================
    // FORGOT PASSWORD (OTP TO EMAIL, AUTO-LOGIN WHEN VERIFIED)
    // ============================================
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $user = User::where('email', $data['email'])->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại trong hệ thống.']);
        }

        $otp = rand(100000, 999999);
        Cache::put('reset_otp_' . $user->email, $otp, 300);
        Session::put('reset_email', $user->email);

        try {
            Mail::to($user->email)->send(new OTPMail($otp));
        } catch (\Exception $e) {
            Log::error('Send reset OTP error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Không thể gửi mã. Vui lòng thử lại.']);
        }

        return redirect()->route('password.verify')->with('success', 'Đã gửi mã xác thực tới email.');
    }

    public function showVerifyResetForm()
    {
        $email = Session::get('reset_email');
        if (!$email) return redirect()->route('password.request');
        return view('auth.verify-reset', compact('email'));
    }

    public function verifyResetOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric|digits:6']);
        $email = Session::get('reset_email');
        if (!$email) return redirect()->route('password.request');

        $cachedOtp = Cache::get('reset_otp_' . $email);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
        }

        $user = User::where('email', $email)->first();
        if (!$user) return redirect()->route('password.request')->withErrors(['email' => 'Email không tồn tại.']);

        // OTP đúng: đăng nhập luôn
        Auth::login($user, true);

        Cache::forget('reset_otp_' . $email);
        Session::forget('reset_email');

        return $this->redirectBasedOnRole($user, 'Xác thực thành công, bạn đã được đăng nhập.');
    }
}

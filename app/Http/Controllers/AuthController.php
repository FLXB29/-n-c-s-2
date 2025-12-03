<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

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

        $user = User::create([
            'full_name' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active'
        ]);

        Auth::login($user);

        return $this->redirectBasedOnRole($user, 'Đăng ký thành công!');
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
            $googleUser = Socialite::driver('google')->user();
            
            return $this->handleSocialLogin($googleUser, 'google');
            
        } catch (\Exception $e) {
            // Log lỗi để debug
            
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
            $facebookUser = Socialite::driver('facebook')->user();
            
            return $this->handleSocialLogin($facebookUser, 'facebook');
            
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Facebook thất bại. Vui lòng thử lại.');
        }
    }

    // ============================================
    // XỬ LÝ SOCIAL LOGIN CHUNG
    // ============================================
    
    protected function handleSocialLogin($socialUser, $provider)
    {
        $email = $socialUser->getEmail();
        
        // Nếu không có email (Facebook không cấp), tạo email giả từ provider ID
        if (!$email) {
            $email = $provider . '_' . $socialUser->getId() . '@social.local';
        }
        
        // Tìm user theo provider ID hoặc email
        $user = User::where($provider . '_id', $socialUser->getId())
                    ->orWhere('email', $email)
                    ->first();

        if ($user) {
            // User đã tồn tại - cập nhật thông tin social
            $user->update([
                $provider . '_id' => $socialUser->getId(),
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        } else {
            // Tạo user mới
            $user = User::create([
                'full_name' => $socialUser->getName(),
                'email' => $email,
                'phone' => null,
                'password_hash' => Hash::make(Str::random(24)), // Random password
                $provider . '_id' => $socialUser->getId(),
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
        $redirect = match($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'organizer' => redirect()->route('organizer.dashboard'),
            default => redirect()->route('home'),
        };

        return $message ? $redirect->with('success', $message) : $redirect;
    }
}
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

        $registerData =[
            'full_name' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active'
        ];

        Session::put('register_data',$registerData);

        $otp = rand(100000,999999);
        Cache::put('verify_email_otp_'.$request->email,$otp,300);

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
        Mail::to($request->email)->send(new OTPMail($otp));
    } catch (\Exception $e) {
        return back()->withErrors(['email' => 'Không thể gửi email xác thực. Vui lòng thử lại.']);
    }

    return redirect()->route('register.verify');
    }

public function showVerifyForm()
{
    if (!Session::has('register_data')) {
        return redirect()->route('register');
    }

    $email = Session::get('register_data')['email'];
    return view('auth.verify-email', compact('email'));
}

public function verifyEmail(Request $request)
{
    $request->validate([
        'otp' => 'required|numeric|digits:6',
    ]);

    $data = Session::get('register_data');
    if (!$data) return redirect()->route('register');

    $cachedOtp = Cache::get('verify_email_otp_' . $data['email']);

    if (!$cachedOtp || $cachedOtp != $request->otp) {
        return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
    }

    $user = User::create([
        'full_name' => $data['full_name'],
        'email'     => $data['email'],
        'phone'     => $data['phone'],
        'password_hash' => $data['password_hash'],
        'role'      => 'user',
        'status'    => 'active',
        'email_verified_at' => now(), 
    ]);

    Auth::login($user);

    Session::forget('register_data');
    Cache::forget('verify_email_otp_' . $data['email']);

    return $this->redirectBasedOnRole($user, 'Đăng ký và xác thực thành công!');
}

  
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

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            return $this->handleSocialLogin($googleUser, 'google');
        } catch (\Exception $e) {
            Log::error('Google login error: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Đăng nhập Google thất bại: ' . $e->getMessage());
        }
    }

   
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')
            ->scopes(['public_profile'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            return $this->handleSocialLogin($facebookUser, 'facebook');
        } catch (\Exception $e) {
            Log::error('Facebook login error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Đăng nhập Facebook thất bại: ' . $e->getMessage());
        }
    }


    protected function handleSocialLogin($socialUser, $provider)
    {
        $email = $socialUser->getEmail();
        $providerId = $socialUser->getId();

        if (!$email) {
            $email = $provider . '_' . $providerId . '@social.local';
        }

        $user = User::where($provider . '_id', $providerId)->first();

        if (!$user) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            $user->update([
                $provider . '_id' => $providerId,
                'avatar' => $user->avatar ?? $socialUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'full_name' => $socialUser->getName(),
                'email' => $email,
                'phone' => null,
                'password_hash' => Hash::make(Str::random(24)),
                $provider . '_id' => $providerId,
                'avatar' => $socialUser->getAvatar(),
                'role' => 'user',
                'status' => 'active',
            ]);
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')
                ->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        Auth::login($user, true);

        return $this->redirectBasedOnRole($user, 'Đăng nhập thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Đăng xuất thành công!');
    }


    protected function redirectBasedOnRole($user, $message = null)
    {
        $redirect = match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'organizer' => redirect()->route('organizer.dashboard'),
            default => redirect()->route('home'),
        };

        return $message ? $redirect->with('success', $message) : $redirect;
    }
}

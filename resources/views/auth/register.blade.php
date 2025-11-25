@extends('layouts.app')

@section('title', 'Đăng ký - EventHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const form = document.querySelector('.auth-form');

            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Mật khẩu xác nhận không khớp");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            password.onchange = validatePassword;
            confirmPassword.onkeyup = validatePassword;
        });
    </script>
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Tạo tài khoản mới</h1>
            <p>Đăng ký để bắt đầu đặt vé sự kiện</p>
        </div>

        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="fullName">Họ và tên *</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="fullName" value="{{ old('fullName') }}" required>
                    </div>
                    @error('fullName') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại *</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="tel" name="phone" value="{{ old('phone') }}" required>
                    </div>
                    @error('phone') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>
                @error('email') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" required minlength="6" 
                           oninvalid="this.setCustomValidity('Mật khẩu phải có ít nhất 6 ký tự')"
                           oninput="this.setCustomValidity('')">
                </div>
                @error('password') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="confirmPassword">Xác nhận mật khẩu *</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" required minlength="6">
                </div>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    <span>Tôi đồng ý với điều khoản sử dụng</span>
                </label>
                @error('terms') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary btn-large btn-block">
                <span>Đăng ký</span>
                <i class="fas fa-arrow-right"></i>
            </button>

            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="{{ route('login') }}" class="link">Đăng nhập ngay</a></p>
            </div>
        </form>
    </div>
    
    <div class="auth-aside">
        <div class="auth-aside-content">
            <i class="fas fa-users auth-icon"></i>
            <h2>Tham gia cộng đồng EventHub</h2>
        </div>
    </div>
</div>
@endsection
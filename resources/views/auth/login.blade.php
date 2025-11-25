@extends('layouts.app') {{-- Giả sử bạn có layout chính --}}

@section('title', 'Đăng nhập - EventHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Chào mừng trở lại!</h1>
            <p>Đăng nhập để tiếp tục trải nghiệm</p>
        </div>

        {{-- Hiển thị lỗi chung nếu có --}}
        @if($errors->any())
            <div style="color: red; margin-bottom: 15px; text-align: center;">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="auth-form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email *</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                @error('email') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu *</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>

            <div class="form-options">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="#" class="link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-primary btn-large btn-block">
                <span>Đăng nhập</span>
                <i class="fas fa-arrow-right"></i>
            </button>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="{{ route('register') }}" class="link">Đăng ký ngay</a></p>
            </div>
        </form>
    </div>
    
    {{-- Phần ảnh bên phải giữ nguyên --}}
    <div class="auth-aside">
        <div class="auth-aside-content">
            <i class="fas fa-ticket-alt auth-icon"></i>
            <h2>Khám phá sự kiện tuyệt vời</h2>
            <p>Tham gia cộng đồng hàng ngàn người yêu thích sự kiện.</p>
        </div>
    </div>
</div>
@endsection
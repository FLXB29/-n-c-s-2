@extends('layouts.app')

@section('title', 'Quên mật khẩu - EventHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Quên mật khẩu?</h1>
            <p>Nhập email để nhận mã xác thực đăng nhập</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 15px;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 15px;">{{ $errors->first() }}</div>
        @endif

        <form class="auth-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email *</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                @error('email') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary btn-large btn-block">
                <span>Gửi mã xác thực</span>
                <i class="fas fa-paper-plane"></i>
            </button>

            <div class="auth-footer">
                <p>Nhớ mật khẩu rồi? <a href="{{ route('login') }}" class="link">Đăng nhập</a></p>
            </div>
        </form>
    </div>

    <div class="auth-aside">
        <div class="auth-aside-content">
            <i class="fas fa-shield-alt auth-icon"></i>
            <h2>Bảo vệ tài khoản của bạn</h2>
            <p>Chỉ bạn mới có thể truy cập hộp thư để lấy mã đăng nhập.</p>
        </div>
    </div>
</div>
@endsection

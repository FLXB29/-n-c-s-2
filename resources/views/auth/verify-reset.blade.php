@extends('layouts.app')

@section('title', 'Xác thực mã - EventHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Nhập mã xác thực</h1>
            <p>Mã đã gửi tới: <strong>{{ $email }}</strong></p>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 15px;">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 15px;">{{ $errors->first() }}</div>
        @endif

        <form class="auth-form" method="POST" action="{{ route('password.verify.submit') }}">
            @csrf
            <div class="form-group">
                <label for="otp">Mã OTP (6 số)</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" id="otp" name="otp" pattern="\d{6}" maxlength="6" required>
                </div>
                @error('otp') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary btn-large btn-block">
                <span>Xác thực & Đăng nhập</span>
                <i class="fas fa-unlock"></i>
            </button>

            <div class="auth-footer">
                <p>Không nhận được mã? <a href="{{ route('password.request') }}" class="link">Gửi lại</a></p>
            </div>
        </form>
    </div>

    <div class="auth-aside">
        <div class="auth-aside-content">
            <i class="fas fa-envelope-open-text auth-icon"></i>
            <h2>Kiểm tra hộp thư</h2>
            <p>Nhập đúng mã trong 5 phút để đăng nhập an toàn.</p>
        </div>
    </div>
</div>
@endsection

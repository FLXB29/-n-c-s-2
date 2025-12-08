@extends('layouts.app')

@section('title', 'Xác thực Email - EventHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1>Xác thực Email</h1>
            <p>Chúng tôi đã gửi mã 6 số đến <strong>{{ $email }}</strong></p>
        </div>

        <form class="auth-form" method="POST" action="{{ url('/register/verify') }}">
            @csrf
            
            <div class="form-group">
                <label for="otp">Mã OTP *</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="text" name="otp" placeholder="Nhập 6 số" required maxlength="6" style="letter-spacing: 4px; font-weight: bold; text-align: center;">
                </div>
                @error('otp') <span class="error-message" style="display:block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="btn-primary btn-large btn-block">
                <span>Xác nhận & Hoàn tất</span>
            </button>

            <div class="auth-footer">
                <p>Không nhận được mã? <a href="{{ route('register') }}" class="link">Đăng ký lại</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
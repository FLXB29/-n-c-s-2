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
            const termsLink = document.getElementById('termsLink');
            const termsModal = document.getElementById('termsModal');
            const termsClose = document.getElementById('termsClose');
            const termsAccept = document.getElementById('termsAccept');
            const termsCheckbox = document.getElementById('termsCheckbox');

            function validatePassword() {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Mật khẩu xác nhận không khớp");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            password.onchange = validatePassword;
            confirmPassword.onkeyup = validatePassword;

            function openTerms() {
                if (termsModal) termsModal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeTerms() {
                if (termsModal) termsModal.classList.remove('show');
                document.body.style.overflow = '';
            }

            if (termsLink) {
                termsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    openTerms();
                });
            }

            if (termsClose) termsClose.addEventListener('click', closeTerms);

            if (termsAccept) {
                termsAccept.addEventListener('click', function() {
                    if (termsCheckbox) termsCheckbox.checked = true;
                    closeTerms();
                });
            }

            // Close when clicking outside the modal box
            if (termsModal) {
                termsModal.addEventListener('click', function(e) {
                    if (e.target === termsModal) closeTerms();
                });
            }
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
                    <input type="checkbox" id="termsCheckbox" name="terms" required>
                    <span>Tôi đồng ý với <a href="#" id="termsLink">điều khoản sử dụng</a></span>
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

<!-- Terms Modal -->
<style>
    .modal-backdrop-custom {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }
    .modal-backdrop-custom.show { display: flex; }
    .modal-box-custom {
        background: #fff;
        max-width: 700px;
        width: 100%;
        border-radius: 12px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.18);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
    }
    .modal-header-custom {
        padding: 16px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .modal-body-custom {
        padding: 16px 20px;
        overflow-y: auto;
    }
    .modal-footer-custom {
        padding: 14px 20px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background: #fafafa;
    }
    .modal-close-btn {
        border: none;
        background: transparent;
        font-size: 18px;
        cursor: pointer;
    }
    .btn-ghost {
        border: 1px solid #ddd;
        background: #fff;
        padding: 8px 14px;
        border-radius: 8px;
        cursor: pointer;
    }
    .btn-primary-fill {
        border: none;
        background: #4f46e5;
        color: #fff;
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
    }
    .terms-list li { margin-bottom: 10px; }
    @media (max-width: 640px) {
        .modal-box-custom { max-width: 100%; }
    }
</style>

<div id="termsModal" class="modal-backdrop-custom">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h4 style="margin: 0;">Điều khoản sử dụng</h4>
            <button id="termsClose" class="modal-close-btn" aria-label="Đóng">×</button>
        </div>
        <div class="modal-body-custom">
            <p>Vui lòng đọc kỹ các điều khoản trước khi tạo tài khoản và sử dụng EventHub.</p>
            <ul class="terms-list">
                <li><strong>Tài khoản & bảo mật:</strong> Bạn chịu trách nhiệm bảo mật mật khẩu và hoạt động trong tài khoản.</li>
                <li><strong>Thông tin chính xác:</strong> Cung cấp họ tên, email, số điện thoại đúng để nhận vé và thông báo.</li>
                <li><strong>Thanh toán & hoàn tiền:</strong> Vé đã thanh toán có thể không hoàn/đổi tuỳ chính sách sự kiện. Kiểm tra kỹ trước khi thanh toán.</li>
                <li><strong>Hành vi sử dụng:</strong> Không spam, không giả mạo, không can thiệp trái phép hệ thống.</li>
                <li><strong>Quyền cập nhật:</strong> EventHub có thể cập nhật điều khoản, bạn tiếp tục sử dụng nghĩa là chấp nhận bản cập nhật.</li>
                <li><strong>Quyền riêng tư:</strong> Dữ liệu được xử lý theo chính sách bảo mật; bạn có quyền yêu cầu chỉnh sửa/xóa theo quy định.</li>
            </ul>
            <p>Nếu bạn đồng ý với các nội dung trên, hãy bấm “Tôi đồng ý” để tiếp tục.</p>
        </div>
        <div class="modal-footer-custom">
            <button class="btn-ghost" id="termsClose">Đóng</button>
            <button class="btn-primary-fill" id="termsAccept">Tôi đồng ý</button>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Dashboard - Hồ sơ cá nhân')

@section('content')
<div class="dashboard-container" style="display: flex; min-height: calc(100vh - 80px); background: #f8fafc;">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar" style="width: 280px; background: white; border-right: 1px solid #e2e8f0; padding: 20px 0;">
        <div class="sidebar-menu">
            <a href="#overview" class="menu-item active" onclick="showSection('overview')" style="display: flex; align-items: center; padding: 12px 24px; color: #64748b; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-th-large" style="width: 24px;"></i>
                <span>Tổng quan</span>
            </a>
            <a href="#profile" class="menu-item" onclick="showSection('profile')" style="display: flex; align-items: center; padding: 12px 24px; color: #64748b; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-user" style="width: 24px;"></i>
                <span>Hồ sơ cá nhân</span>
            </a>
            <a href="#tickets" class="menu-item" onclick="showSection('tickets')" style="display: flex; align-items: center; padding: 12px 24px; color: #64748b; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-ticket-alt" style="width: 24px;"></i>
                <span>Vé của tôi</span>
            </a>
            <a href="#password" class="menu-item" onclick="showSection('password')" style="display: flex; align-items: center; padding: 12px 24px; color: #64748b; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-key" style="width: 24px;"></i>
                <span>Đổi mật khẩu</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main" style="flex: 1; padding: 30px;">
        @if(session('success'))
            <div class="alert alert-success" style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Overview Section -->
        <section id="overview-section" class="dashboard-section active">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">Tổng quan</h1>
                <p style="color: #64748b;">Xin chào, {{ $user->full_name }}! Đây là thông tin tài khoản của bạn.</p>
            </div>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                <div class="stat-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 20px;">
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #e0e7ff; color: #4338ca; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">{{ ucfirst($user->role) }}</h3>
                        <p style="color: #64748b; margin: 0;">Vai trò</p>
                    </div>
                </div>
                
                <div class="stat-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 20px;">
                    <div class="stat-icon" style="width: 50px; height: 50px; background: #d1fae5; color: #059669; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 style="font-size: 24px; font-weight: 700; color: #1e293b; margin: 0;">{{ $user->status == 'active' ? 'Hoạt động' : 'Bị khóa' }}</h3>
                        <p style="color: #64748b; margin: 0;">Trạng thái</p>
                    </div>
                </div>
            </div>

            <!-- Organizer Request Section -->
            @if($user->role === 'user')
                <div class="mt-4" style="margin-top: 30px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 5px;">Trở thành Nhà tổ chức sự kiện</h3>
                            <p style="color: #64748b; margin: 0;">Đăng ký để tạo và quản lý các sự kiện của riêng bạn trên EventHub.</p>
                        </div>
                        
                        @if($user->organizer_request_status === 'pending')
                            <button disabled class="btn btn-warning" style="background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: not-allowed; opacity: 0.8;">
                                <i class="fas fa-clock"></i> Đang chờ duyệt
                            </button>
                        @elseif($user->organizer_request_status === 'rejected')
                            <div>
                                <span class="text-danger d-block mb-2">Yêu cầu trước đó đã bị từ chối.</span>
                                <form action="{{ route('user.requestOrganizer') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="background: #4f46e5; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">
                                        <i class="fas fa-paper-plane"></i> Gửi lại yêu cầu
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('user.requestOrganizer') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="background: #4f46e5; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: background 0.3s;">
                                    <i class="fas fa-arrow-up"></i> Nâng cấp tài khoản
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <!-- Tickets Section -->
        <section id="tickets-section" class="dashboard-section" style="display: none;">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">Vé của tôi</h1>
                <p style="color: #64748b;">Danh sách các vé bạn đã mua.</p>
            </div>

            <div class="tickets-list">
                @if(isset($orders) && $orders->count() > 0)
                    @foreach($orders as $order)
                        <div class="ticket-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 5px;">
                                        {{ $order->event->title ?? 'Sự kiện không tồn tại' }}
                                    </h3>
                                    <p style="color: #64748b; margin-bottom: 5px;">
                                        <i class="fas fa-calendar"></i> {{ $order->event->start_time ? \Carbon\Carbon::parse($order->event->start_time)->format('d/m/Y H:i') : 'N/A' }}
                                    </p>
                                    <p style="color: #64748b;">
                                        <i class="fas fa-map-marker-alt"></i> {{ $order->event->venue_name ?? 'N/A' }}
                                    </p>
                                </div>
                                <div style="text-align: right;">
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark" style="padding: 5px 10px; border-radius: 20px; font-size: 12px;">CHỜ THANH TOÁN</span>
                                        <p style="font-weight: bold; color: #4f46e5; margin-top: 10px;">{{ number_format($order->final_amount) }} VNĐ</p>
                                        <a href="{{ route('orders.payment', $order->id) }}" class="btn btn-sm btn-primary mt-2">Thanh toán ngay</a>
                                    @else
                                        <span class="badge bg-success" style="padding: 5px 10px; border-radius: 20px; font-size: 12px;">{{ strtoupper($order->status) }}</span>
                                        <p style="font-weight: bold; color: #4f46e5; margin-top: 10px;">{{ number_format($order->final_amount) }} VNĐ</p>
                                    @endif
                                </div>
                            </div>
                            <hr style="border-top: 1px solid #e2e8f0; margin: 15px 0;">
                            <div class="ticket-items">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 10px;">Chi tiết vé:</h4>
                                <ul style="list-style: none; padding: 0;">
                                    @foreach($order->tickets as $ticket)
                                        <li style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                                            <span>
                                                <i class="fas fa-ticket-alt"></i> {{ $ticket->ticketType->name ?? 'Vé' }}
                                                <small class="text-muted">({{ $ticket->ticket_code }})</small>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state" style="text-align: center; padding: 40px;">
                        <img src="https://via.placeholder.com/150?text=No+Tickets" alt="No Tickets" style="margin-bottom: 20px; opacity: 0.5;">
                        <p style="color: #64748b;">Bạn chưa mua vé nào.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-primary" style="margin-top: 10px;">Khám phá sự kiện</a>
                    </div>
                @endif
            </div>
        </section>

        <!-- Profile Section -->
        <section id="profile-section" class="dashboard-section" style="display: none;">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">Hồ sơ cá nhân</h1>
                <p style="color: #64748b;">Cập nhật thông tin tài khoản của bạn</p>
            </div>

            <div class="dashboard-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <!-- Avatar Upload -->
                <div class="profile-header" style="display: flex; align-items: center; gap: 30px; margin-bottom: 40px; padding-bottom: 30px; border-bottom: 1px solid #e2e8f0;">
                    <div class="profile-avatar-wrapper" style="position: relative;">
                        <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/default-avatar.png') }}" 
                             alt="Avatar" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #f1f5f9;">
                        
                        <form action="{{ route('user.avatar.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                            @csrf
                            <label for="avatar-input" style="position: absolute; bottom: 0; right: 0; background: #4f46e5; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <i class="fas fa-camera" style="font-size: 14px;"></i>
                            </label>
                            <input type="file" name="avatar" id="avatar-input" style="display: none;" onchange="document.getElementById('avatar-form').submit()">
                        </form>
                    </div>
                    <div class="profile-header-info">
                        <h2 style="font-size: 20px; color: #1e293b; margin-bottom: 5px;">{{ $user->full_name }}</h2>
                        <p style="color: #64748b; margin-bottom: 10px;">{{ $user->email }}</p>
                        <div class="profile-badges">
                            <span style="background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Thành viên từ {{ $user->created_at->format('Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Form -->
                <form action="{{ route('user.profile.update') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Họ và tên *</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Ngày sinh</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}"
                                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                        </div>
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Giới tính</label>
                            <select name="gender" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                                <option value="">Chọn giới tính</option>
                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Địa chỉ</label>
                        <textarea name="address" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="background: #4f46e5; color: white; padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save" style="margin-right: 8px;"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Password Section -->
        <section id="password-section" class="dashboard-section" style="display: none;">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">Đổi mật khẩu</h1>
                <p style="color: #64748b;">Bảo mật tài khoản của bạn</p>
            </div>

            <div class="dashboard-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 600px;">
                <form action="{{ route('user.password.change') }}" method="POST" class="dashboard-form">
                    @csrf
                    
                    <!-- Mật khẩu hiện tại -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control" required
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                        @error('current_password') <span class="error-message" style="color: red; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phần nhập OTP -->
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Mã xác thực (OTP)</label>
                        <div class="otp-input-group" style="display: flex; gap: 10px;">
                            <input type="text" name="otp" class="form-control" placeholder="Nhập mã 6 số từ email" required
                                   style="flex: 1; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                            <button type="button" id="btnSendOtp" class="btn-secondary" style="white-space: nowrap; padding: 10px 20px; background: #64748b; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                Gửi mã OTP
                            </button>
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 5px; color: #64748b;">Nhấn "Gửi mã OTP" và kiểm tra email của bạn.</small>
                        @error('otp') <span class="error-message" style="color: red; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                        @error('new_password') <span class="error-message" style="color: red; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="display: block; margin-bottom: 8px; color: #475569; font-weight: 500;">Xác nhận mật khẩu mới</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required
                               style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="background: #4f46e5; color: white; padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-save" style="margin-right: 8px;"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>

@push('styles')
<style>
    .menu-item:hover, .menu-item.active {
        background: #f1f5f9;
        color: #4f46e5 !important;
        border-right: 3px solid #4f46e5;
    }
</style>
@endpush

<script>
    function showSection(sectionName) {
        // Hide all sections
        const sections = document.querySelectorAll('.dashboard-section');
        sections.forEach(section => {
            section.style.display = 'none';
        });

        // Show selected section
        const targetSection = document.getElementById(sectionName + '-section');
        if (targetSection) {
            targetSection.style.display = 'block';
        }

        // Update sidebar active state
        const menuItems = document.querySelectorAll('.menu-item');
        menuItems.forEach(item => {
            item.classList.remove('active');
        });

        // Find the link that was clicked (or corresponds to the section)
        const activeMenuItem = document.querySelector(`a[href="#${sectionName}"]`);
        if (activeMenuItem) {
            activeMenuItem.classList.add('active');
        }
    }

    document.getElementById('btnSendOtp').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerText;
        
        // Disable nút để tránh bấm nhiều lần
        btn.disabled = true;
        btn.innerText = 'Đang gửi...';

        fetch('{{ route("user.sendOtp") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if(data.success) {
                // Đếm ngược 60s mới cho gửi lại
                let count = 60;
                const interval = setInterval(() => {
                    btn.innerText = `Gửi lại (${count}s)`;
                    count--;
                    if(count < 0) {
                        clearInterval(interval);
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                }, 1000);
            } else {
                btn.disabled = false;
                btn.innerText = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại.');
            btn.disabled = false;
            btn.innerText = originalText;
        });
    });
</script>
@endsection

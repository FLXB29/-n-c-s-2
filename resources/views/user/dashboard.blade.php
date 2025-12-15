@extends('layouts.app')

@section('title', 'Dashboard - Hồ sơ cá nhân')

@section('content')
<!-- Sidebar Toggle Button for Mobile -->
<button class="user-sidebar-toggle" id="userSidebarToggle" onclick="toggleUserSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Overlay -->
<div class="user-sidebar-overlay" id="userSidebarOverlay" onclick="toggleUserSidebar()"></div>

<div class="dashboard-container" style="display: flex; min-height: calc(100vh - 80px); background: #f8fafc;">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar" id="userDashboardSidebar" style="width: 280px; background: white; border-right: 1px solid #e2e8f0; padding: 20px 0;">
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
            @if(!$user->isSocialAccount())
            <a href="#password" class="menu-item" onclick="showSection('password')" style="display: flex; align-items: center; padding: 12px 24px; color: #64748b; text-decoration: none; transition: all 0.3s;">
                <i class="fas fa-key" style="width: 24px;"></i>
                <span>Đổi mật khẩu</span>
            </a>
            @endif
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
                <p style="color: #64748b;">Danh sách các vé bạn đã mua. Nhấn vào vé để xem mã QR check-in.</p>
            </div>

            <!-- Tickets Stats -->
            @if(isset($orders) && $orders->count() > 0)
            <div class="tickets-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
                @php
                    $totalTickets = $orders->sum(fn($o) => $o->tickets->count());
                    $activeTickets = $orders->sum(fn($o) => $o->tickets->where('status', 'active')->count());
                    $usedTickets = $orders->sum(fn($o) => $o->tickets->where('status', 'used')->count());
                @endphp
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 20px; border-radius: 10px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;">{{ $totalTickets }}</div>
                    <div style="font-size: 13px; opacity: 0.9;">Tổng số vé</div>
                </div>
                <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; padding: 15px 20px; border-radius: 10px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;">{{ $activeTickets }}</div>
                    <div style="font-size: 13px; opacity: 0.9;">Chưa sử dụng</div>
                </div>
                <div style="background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); color: white; padding: 15px 20px; border-radius: 10px; text-align: center;">
                    <div style="font-size: 24px; font-weight: 700;">{{ $usedTickets }}</div>
                    <div style="font-size: 13px; opacity: 0.9;">Đã check-in</div>
                </div>
            </div>
            @endif

            <div class="tickets-list">
                @if(isset($orders) && $orders->count() > 0)
                    @foreach($orders as $order)
                        <div class="ticket-card" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div>
                                    <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 5px;">
                                        @if($order->event)
                                            <a href="{{ route('events.show', $order->event->id) }}" style="color: rgb(83, 83, 216); text-decoration: none;">
                                                {{ $order->event->title }}
                                            </a>
                                        @else
                                            Sự kiện không tồn tại
                                        @endif
                                    </h3>
                                    <p style="color: #64748b; margin-bottom: 5px;">
                                        <i class="fas fa-calendar"></i> {{ $order->event && $order->event->start_datetime ? $order->event->start_datetime->format('d/m/Y H:i') : 'N/A' }}
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
                                        <p style="color: #64748b; font-size: 13px; margin: 4px 0 0;">
                                            <i class="fas fa-clock"></i> Thanh toán lúc: {{ optional($order->updated_at)->format('d/m/Y H:i') ?? 'N/A' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <hr style="border-top: 1px solid #e2e8f0; margin: 15px 0;">
                            
                            <!-- Ticket Items with QR -->
                            <div class="ticket-items">
                                <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 15px;">
                                    <i class="fas fa-ticket-alt"></i> Chi tiết vé ({{ $order->tickets->count() }} vé):
                                </h4>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                    @foreach($order->tickets as $ticket)
                                        <div class="single-ticket" 
                                             style="background: {{ $ticket->status == 'used' ? '#f1f5f9' : '#f8fafc' }}; 
                                                    border: 2px solid {{ $ticket->status == 'used' ? '#cbd5e1' : '#e2e8f0' }}; 
                                                    border-radius: 10px; 
                                                    padding: 15px; 
                                                    cursor: pointer;
                                                    transition: all 0.3s ease;
                                                    {{ $ticket->status == 'used' ? 'opacity: 0.7;' : '' }}"
                                             onclick="showTicketQR({{ json_encode([
                                                 'ticket_code' => $ticket->ticket_code,
                                                 'event_name' => $order->event->title ?? 'N/A',
                                                 'ticket_type' => $ticket->ticketType->name ?? 'Vé thường',
                                                 'seat' => $ticket->seat ? ($ticket->seat->section . ' - Hàng ' . $ticket->seat->row_number . ' - Số ' . $ticket->seat->seat_number) : 'Không chọn ghế',
                                                 'status' => $ticket->status,
                                                 'price_paid' => number_format($ticket->price_paid, 0, ',', '.') . ' đ',
                                                 'event_date' => $order->event && $order->event->start_datetime ? $order->event->start_datetime->format('d/m/Y H:i') : 'N/A',
                                                 'venue' => $order->event->venue_name ?? 'N/A',
                                                 'check_in_time' => $ticket->check_in_time ? $ticket->check_in_time->format('d/m/Y H:i:s') : null,
                                             ]) }})"
                                             onmouseover="this.style.borderColor='#6c5ce7'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(108,92,231,0.2)';"
                                             onmouseout="this.style.borderColor='{{ $ticket->status == 'used' ? '#cbd5e1' : '#e2e8f0' }}'; this.style.transform='none'; this.style.boxShadow='none';">
                                            
                                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                                <div>
                                                    <span style="font-family: monospace; font-size: 12px; color: #6c5ce7; font-weight: 600;">
                                                        {{ $ticket->ticket_code }}
                                                    </span>
                                                    <div style="font-weight: 600; color: #1e293b; margin-top: 5px;">
                                                        {{ $ticket->ticketType->name ?? 'Vé thường' }}
                                                    </div>
                                                </div>
                                                <span style="padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;
                                                    {{ $ticket->status == 'active' ? 'background: #d1fae5; color: #065f46;' : '' }}
                                                    {{ $ticket->status == 'used' ? 'background: #fef3c7; color: #92400e;' : '' }}
                                                    {{ $ticket->status == 'cancelled' ? 'background: #fee2e2; color: #991b1b;' : '' }}">
                                                    @if($ticket->status == 'active')
                                                        <i class="fas fa-check-circle"></i> Chưa sử dụng
                                                    @elseif($ticket->status == 'used')
                                                        <i class="fas fa-check-double"></i> Đã check-in
                                                    @else
                                                        {{ ucfirst($ticket->status) }}
                                                    @endif
                                                </span>
                                            </div>
                                            
                                            @if($ticket->seat)
                                                <div style="font-size: 13px; color: #64748b; margin-bottom: 8px;">
                                                    <i class="fas fa-chair"></i> 
                                                    {{ $ticket->seat->section ?? 'Khu' }} - Hàng {{ $ticket->seat->row_number ?? '?' }} - Số {{ $ticket->seat->seat_number ?? '?' }}
                                                </div>
                                            @endif
                                            
                                            <div style="font-size: 13px; color: #64748b; margin-bottom: 10px;">
                                                <i class="fas fa-money-bill"></i> {{ number_format($ticket->price_paid, 0, ',', '.') }} đ
                                            </div>
                                            
                                            @if($ticket->check_in_time)
                                                <div style="font-size: 12px; color: #059669; background: #d1fae5; padding: 5px 10px; border-radius: 6px; text-align: center;">
                                                    <i class="fas fa-clock"></i> Check-in: {{ $ticket->check_in_time->format('d/m/Y H:i') }}
                                                </div>
                                            @else
                                                <button style="width: 100%; padding: 8px; background: linear-gradient(135deg, #6c5ce7, #a29bfe); color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;"
                                                        onclick="event.stopPropagation(); showTicketQR({{ json_encode([
                                                            'ticket_code' => $ticket->ticket_code,
                                                            'event_name' => $order->event->title ?? 'N/A',
                                                            'ticket_type' => $ticket->ticketType->name ?? 'Vé thường',
                                                            'seat' => $ticket->seat ? ($ticket->seat->section . ' - Hàng ' . $ticket->seat->row_number . ' - Số ' . $ticket->seat->seat_number) : 'Không chọn ghế',
                                                            'status' => $ticket->status,
                                                            'price_paid' => number_format($ticket->price_paid, 0, ',', '.') . ' đ',
                                                            'event_date' => $order->event && $order->event->start_datetime ? $order->event->start_datetime->format('d/m/Y H:i') : 'N/A',
                                                            'venue' => $order->event->venue_name ?? 'N/A',
                                                            'check_in_time' => null,
                                                        ]) }})">
                                                    <i class="fas fa-qrcode"></i> Xem mã QR
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
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
        @if(!$user->isSocialAccount())
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
        @else
        <section id="password-section" class="dashboard-section" style="display: none;">
            <div class="section-header" style="margin-bottom: 30px;">
                <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 10px;">Đổi mật khẩu</h1>
            </div>
            <div class="dashboard-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 600px;">
                <div class="alert alert-info" style="background: #e0f2fe; color: #0369a1; padding: 20px; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-info-circle" style="font-size: 24px;"></i>
                    <div>
                        <p style="margin: 0; font-weight: 600; margin-bottom: 5px;">Tài khoản đăng nhập bằng
                            @if($user->google_id)
                                <i class="fab fa-google" style="color: #ea4335;"></i> Google
                            @endif
                            @if($user->facebook_id)
                                <i class="fab fa-facebook" style="color: #1877f2;"></i> Facebook
                            @endif
                        </p>
                        <p style="margin: 0; font-size: 14px;">Bạn không cần mật khẩu để đăng nhập. Tính năng đổi mật khẩu không khả dụng cho tài khoản này.</p>
                    </div>
                </div>
            </div>
        </section>
        @endif
    </main>
</div>

@push('styles')
<style>
    .menu-item:hover, .menu-item.active {
        background: #f1f5f9;
        color: #4f46e5 !important;
        border-right: 3px solid #4f46e5;
    }
    
    /* User Sidebar Toggle Button */
    .user-sidebar-toggle {
        display: none;
        position: fixed;
        left: 20px;
        bottom: 24px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: white;
        border: none;
        cursor: pointer;
        z-index: 1050;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4);
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.3s ease;
    }
    
    .user-sidebar-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5);
    }
    
    .user-sidebar-toggle.active i::before {
        content: "\f00d";
    }
    
    /* User Sidebar Overlay */
    .user-sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .user-sidebar-overlay.active {
        display: block;
        opacity: 1;
    }
    
    /* Responsive Styles */
    @media (max-width: 992px) {
        .user-sidebar-toggle {
            display: flex !important;
        }
        
        .dashboard-container {
            flex-direction: column !important;
        }
        
        .dashboard-sidebar {
            position: fixed !important;
            top: 70px !important;
            left: 0 !important;
            width: 280px !important;
            height: calc(100vh - 70px) !important;
            z-index: 1001 !important;
            transform: translateX(-100%) !important;
            transition: transform 0.3s ease !important;
            overflow-y: auto !important;
        }
        
        .dashboard-sidebar.active {
            transform: translateX(0) !important;
        }
        
        .dashboard-main {
            padding: 20px 15px !important;
            width: 100% !important;
        }
        
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .profile-header {
            flex-direction: column !important;
            text-align: center !important;
            gap: 15px !important;
        }
        
        .profile-header-info {
            text-align: center !important;
        }
        
        /* Profile form 2 columns -> 1 column */
        .dashboard-card form > div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        /* Ticket card responsive */
        .ticket-card > div[style*="display: flex"][style*="justify-content: space-between"] {
            flex-direction: column !important;
            gap: 15px !important;
        }
        
        .ticket-card > div > div:last-child {
            text-align: left !important;
        }
        
        /* Organizer request section */
        .mt-4 > div[style*="display: flex"][style*="justify-content: space-between"] {
            flex-direction: column !important;
            gap: 15px !important;
            align-items: flex-start !important;
        }
    }
    
    @media (max-width: 480px) {
        .dashboard-main {
            padding: 15px 10px !important;
        }
        
        .section-header h1 {
            font-size: 20px !important;
        }
        
        .stat-card {
            padding: 15px !important;
        }
        
        .stat-icon {
            width: 40px !important;
            height: 40px !important;
            font-size: 18px !important;
        }
        
        .stat-info h3 {
            font-size: 18px !important;
        }
        
        .dashboard-card {
            padding: 15px !important;
        }
        
        .profile-avatar-wrapper img {
            width: 80px !important;
            height: 80px !important;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 8px !important;
            font-size: 14px !important;
        }
        
        .otp-input-group {
            flex-direction: column !important;
        }
        
        .otp-input-group button {
            width: 100% !important;
        }
        
        .user-sidebar-toggle {
            left: 15px;
            bottom: 15px;
            width: 50px;
            height: 50px;
            font-size: 18px;
        }
    }
</style>
@endpush

<script>
    // Check on page load if we need to show a specific section
    document.addEventListener('DOMContentLoaded', function() {
        // Check localStorage for section to show
        const savedSection = localStorage.getItem('dashboardSection');
        if (savedSection) {
            showSection(savedSection);
            localStorage.removeItem('dashboardSection');
        }
        
        // Check URL hash
        const hash = window.location.hash.replace('#', '');
        if (hash && ['overview', 'profile', 'tickets', 'password'].includes(hash)) {
            showSection(hash);
        }
    });

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
        
        // Close sidebar on mobile after selecting section
        if (window.innerWidth <= 992) {
            const sidebar = document.getElementById('userDashboardSidebar');
            const overlay = document.getElementById('userSidebarOverlay');
            const toggle = document.getElementById('userSidebarToggle');
            
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (toggle) toggle.classList.remove('active');
        }
    }
    
    // Toggle User Sidebar
    function toggleUserSidebar() {
        const sidebar = document.getElementById('userDashboardSidebar');
        const overlay = document.getElementById('userSidebarOverlay');
        const toggle = document.getElementById('userSidebarToggle');
        
        if (sidebar) sidebar.classList.toggle('active');
        if (overlay) overlay.classList.toggle('active');
        if (toggle) toggle.classList.toggle('active');
    }
    
    // Close sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            const sidebar = document.getElementById('userDashboardSidebar');
            const overlay = document.getElementById('userSidebarOverlay');
            const toggle = document.getElementById('userSidebarToggle');
            
            if (sidebar) sidebar.classList.remove('active');
            if (overlay) overlay.classList.remove('active');
            if (toggle) toggle.classList.remove('active');
        }
    });

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

    // QR Modal close handlers sẽ được setup riêng
</script>

<!-- QRCode.js library - dùng qrcodejs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// QR Code generation function
function generateQR(container, text) {
    console.log('generateQR called with:', text);
    console.log('QRCode available:', typeof QRCode);
    
    // Clear previous QR
    container.innerHTML = '';
    
    if (typeof QRCode !== 'undefined') {
        try {
            new QRCode(container, {
                text: text,
                width: 200,
                height: 200,
                colorDark: '#1a1a2e',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
            console.log('QR generated successfully');
        } catch (error) {
            console.error('QR Error:', error);
            showFallbackQR(container, text);
        }
    } else {
        console.error('QRCode library not loaded');
        showFallbackQR(container, text);
    }
}

function showFallbackQR(container, text) {
    container.innerHTML = `
        <div style="width: 200px; height: 200px; background: #f8f9fa; display: flex; flex-direction: column; align-items: center; justify-content: center; border-radius: 8px;">
            <div style="color: #6c5ce7; font-weight: bold; font-size: 12px;">Mã vé:</div>
            <div style="color: #1a1a2e; font-family: monospace; font-size: 11px; word-break: break-all; padding: 10px; text-align: center;">${text}</div>
        </div>
    `;
}

// QR Code Functions
function showTicketQR(ticketData) {
    console.log('showTicketQR called:', ticketData);
    
    const modal = document.getElementById('qrModal');
    const qrContainer = document.getElementById('qrCodeDiv');
    const infoContainer = document.getElementById('ticketInfoModal');
    const checkInStatus = document.getElementById('checkInStatus');

    if (!modal || !qrContainer) {
        console.error('Modal or qrContainer not found');
        return;
    }

    // Show modal first
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';

    // Generate QR code after a short delay to ensure modal is visible
    setTimeout(function() {
        generateQR(qrContainer, ticketData.ticket_code);
    }, 150);

    // Fill ticket info
    infoContainer.innerHTML = `
        <p><span>Mã vé:</span> <span style="font-family: monospace; color: #6c5ce7;">${ticketData.ticket_code}</span></p>
        <p><span>Sự kiện:</span> <span>${ticketData.event_name}</span></p>
        <p><span>Loại vé:</span> <span>${ticketData.ticket_type}</span></p>
        <p><span>Ghế:</span> <span>${ticketData.seat}</span></p>
        <p><span>Ngày diễn ra:</span> <span>${ticketData.event_date}</span></p>
        <p><span>Địa điểm:</span> <span>${ticketData.venue}</span></p>
        <p><span>Giá vé:</span> <span style="color: #e74c3c; font-weight: 600;">${ticketData.price_paid}</span></p>
    `;

    // Check-in status
    if (ticketData.check_in_time) {
        checkInStatus.className = 'qr-check-in-status checked-in';
        checkInStatus.innerHTML = `<i class="fas fa-check-circle"></i> Đã check-in lúc: ${ticketData.check_in_time}`;
    } else {
        checkInStatus.className = 'qr-check-in-status not-checked';
        checkInStatus.innerHTML = `<i class="fas fa-info-circle"></i> Chưa check-in - Đưa mã QR này cho nhân viên quét`;
    }
}

function closeQRModal() {
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        // Clear QR code when closing
        const qrContainer = document.getElementById('qrCodeDiv');
        if (qrContainer) qrContainer.innerHTML = '';
    }
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('qrModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeQRModal();
            }
        });
    }
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQRModal();
    }
});
</script>

<!-- QR Modal -->
<div class="qr-modal" id="qrModal">
    <div class="qr-modal-content">
        <div class="qr-modal-header">
            <h3><i class="fas fa-qrcode"></i> Mã QR Vé</h3>
            <button class="qr-modal-close" onclick="closeQRModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="qr-modal-body">
            <div class="qr-code-container">
                <div id="qrCodeDiv"></div>
            </div>
            
            <div class="ticket-info-modal" id="ticketInfoModal">
                <!-- Filled by JavaScript -->
            </div>

            <div class="qr-check-in-status" id="checkInStatus">
                <!-- Filled by JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
/* QR Modal Styles */
.qr-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 99999;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}

.qr-modal.active {
    display: flex;
}

.qr-modal-content {
    background: white;
    border-radius: 20px;
    max-width: 400px;
    width: 100%;
    overflow: hidden;
    animation: qrModalSlideIn 0.3s ease;
}

@keyframes qrModalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.qr-modal-header {
    background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
    color: white;
    padding: 1.25rem 1.5rem;
    text-align: center;
    position: relative;
}

.qr-modal-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.qr-modal-close {
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
}

.qr-modal-close:hover {
    background: rgba(255,255,255,0.3);
}

.qr-modal-body {
    padding: 1.5rem;
    text-align: center;
}

.qr-code-container {
    background: white;
    padding: 1rem;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
    border: 3px solid #f1f5f9;
    min-height: 220px;
}

.qr-code-container img {
    display: block;
}

.qr-code-container canvas {
    display: block;
}

.ticket-info-modal {
    text-align: left;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
}

.ticket-info-modal p {
    margin: 0.5rem 0;
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    padding: 0.25rem 0;
    border-bottom: 1px solid #e9ecef;
}

.ticket-info-modal p:last-child {
    border-bottom: none;
}

.ticket-info-modal p span:first-child {
    color: #666;
}

.ticket-info-modal p span:last-child {
    font-weight: 600;
    color: #1a1a2e;
    text-align: right;
    max-width: 60%;
}

.qr-check-in-status {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
}

.qr-check-in-status.checked-in {
    background: #d1fae5;
    color: #065f46;
}

.qr-check-in-status.not-checked {
    background: #e0e7ff;
    color: #3730a3;
}

@media (max-width: 480px) {
    .qr-modal-content {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
    
    .qr-modal-body {
        padding: 1rem;
    }
    
    .qr-code-container {
        padding: 0.75rem;
    }
    
    .ticket-info-modal p {
        font-size: 0.8rem;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .ticket-info-modal p span:last-child {
        max-width: 100%;
        text-align: left;
    }
}
</style>
@endsection

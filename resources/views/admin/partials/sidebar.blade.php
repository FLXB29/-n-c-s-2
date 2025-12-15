<!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                    <a href="{{ route('admin.statistics') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Thống kê</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.events.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý Events</span>
                        @if($stats['pending_events'] > 0)
                            <span class="badge bg-danger rounded-pill ms-auto">{{ $stats['pending_events'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.requests.index') }}">
                        <i class="fas fa-user-clock"></i>
                        <span>Yêu cầu Organizer</span>
                        @if($stats['organizer_requests'] > 0)
                            <span class="badge bg-danger fw-bold rounded-pill ms-auto" style="background-color: #dc3545 !important; color: white !important;">{{ $stats['organizer_requests'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Quản lý Users</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Quản lý Đơn hàng</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.check-in.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.check-in.index') }}">
                        <i class="fas fa-qrcode"></i>
                        <span>Quét QR Check-in</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
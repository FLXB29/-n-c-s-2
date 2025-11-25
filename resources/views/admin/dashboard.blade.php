<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EventHub</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Adjustments for Main Navbar integration */
        body {
            padding-top: 80px; /* Match main navbar height */
        }
        .admin-sidebar {
            top: 80px !important; /* Push sidebar down below main navbar */
            height: calc(100vh - 80px);
        }
        .admin-main {
            margin-top: 0 !important; /* Body padding handles the offset */
            padding-top: 20px;
        }
        /* Ensure sidebar toggle works with main navbar if needed, 
           or hide the custom toggle and rely on main navbar */
    </style>
</head>
<body>
    <!-- Use Main Website Navbar -->
    @include('partials.navbar')

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="menu-item active" data-section="overview">
                    <a href="#" onclick="showSection('overview')">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li class="menu-item" data-section="users">
                    <a href="#" onclick="showSection('users')">
                        <i class="fas fa-users"></i>
                        <span>Quản lý Users</span>
                    </a>
                </li>
                <li class="menu-item" data-section="events">
                    <a href="#" onclick="showSection('events')">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý Events</span>
                    </a>
                </li>
                <li class="menu-item" data-section="organizers">
                    <a href="#" onclick="showSection('organizers')">
                        <i class="fas fa-user-tie"></i>
                        <span>Organizers</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Overview Section -->
        <section id="overview-section" class="dashboard-section active">
            <div class="section-header">
                <h1>Admin Dashboard - Tổng quan hệ thống</h1>
                <p>Quản lý và giám sát toàn bộ platform EventHub</p>
            </div>

            <!-- System Stats -->
            <div class="stats-grid admin-stats">
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_users']) }}</h3>
                        <p>Tổng Users</p>
                        <span class="stat-change positive">+{{ $stats['new_users_this_month'] }} tháng này</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon events">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_events']) }}</h3>
                        <p>Events đang hoạt động</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_organizers']) }}</h3>
                        <p>Organizers</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon tickets">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>--</h3>
                        <p>Vé đã bán</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Users Management Section -->
        <section id="users-section" class="dashboard-section">
            <div class="section-header">
                <h1>Quản lý Users</h1>
                <p>Quản lý tất cả người dùng trên platform</p>
            </div>

            <!-- Users Table -->
            <div class="admin-card table-card">
                <div class="card-header">
                    <h3>Danh sách Users</h3>
                </div>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Email / SĐT</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/default-avatar.png') }}" 
                                             alt="Avatar" 
                                             style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div style="font-weight: 600;">{{ $user->full_name }}</div>
                                            <div style="font-size: 0.8rem; color: #888;">ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->email }}</div>
                                    <div style="font-size: 0.8rem; color: #888;">{{ $user->phone }}</div>
                                </td>
                                <td>
                                    @if($user->role === 'organizer')
                                        <span class="badge badge-organizer">Organizer</span>
                                    @else
                                        <span class="badge badge-user">User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status === 'active')
                                        <span class="badge badge-active">Hoạt động</span>
                                    @else
                                        <span class="badge badge-blocked">Đã chặn</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div style="display: flex; justify-content: flex-end; gap: 5px;">
                                        {{-- Nút đổi quyền --}}
                                        <form action="{{ route('admin.toggleRole', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->role === 'user')
                                                <button type="submit" class="btn-action btn-promote" title="Cấp quyền Organizer">
                                                    <i class="fas fa-arrow-up"></i> <span>Cấp quyền</span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn-action btn-demote" title="Hủy quyền Organizer">
                                                    <i class="fas fa-arrow-down"></i> <span>Hủy quyền</span>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- Nút chặn/bỏ chặn --}}
                                        <form action="{{ route('admin.toggleStatus', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            @if($user->status === 'active')
                                                <button type="submit" class="btn-action btn-block-user" title="Chặn người dùng" onclick="return confirm('Bạn có chắc muốn chặn người dùng này?')">
                                                    <i class="fas fa-ban"></i> <span>Chặn</span>
                                                </button>
                                            @else
                                                <button type="submit" class="btn-action btn-unblock" title="Bỏ chặn">
                                                    <i class="fas fa-check"></i> <span>Bỏ chặn</span>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Events Management Section -->
        <section id="events-section" class="dashboard-section">
            <div class="section-header">
                <h1>Quản lý Events</h1>
                <p>Danh sách sự kiện mới nhất</p>
            </div>
            
            <div class="admin-card table-card">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tên sự kiện</th>
                                <th>Organizer</th>
                                <th>Thời gian</th>
                                <th>Địa điểm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->organizer->full_name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}</td>
                                <td>
                                    {{ $event->venue_name ? $event->venue_name . ', ' : '' }}
                                    {{ $event->venue_address ? $event->venue_address . ', ' : '' }}
                                    {{ $event->venue_city }}
                                    {{ $event->venue_country ? ', ' . $event->venue_country : '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Organizers Section -->
        <section id="organizers-section" class="dashboard-section">
            <div class="section-header">
                <h1>Quản lý Organizers</h1>
                <p>Danh sách nhà tổ chức</p>
            </div>
            <div class="admin-card">
                <p>Tính năng đang phát triển...</p>
            </div>
        </section>

    </main>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.dashboard-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Show selected section
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.classList.add('active');
            }

            // Update sidebar active state
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.classList.remove('active');
            });

            const activeMenuItem = document.querySelector(`[data-section="${sectionName}"]`);
            if (activeMenuItem) {
                activeMenuItem.classList.add('active');
            }
        }
    </script>
</body>
</html>
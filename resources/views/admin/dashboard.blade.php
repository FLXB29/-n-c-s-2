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
    @include('admin.partials.sidebar')
    <!-- Sidebar -->
    {{-- <aside class="admin-sidebar" id="sidebar">
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
            </ul>
        </div>
    </aside> --}}

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
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon events">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['total_events']) }}</h3>
                        <p>Tổng Events</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['pending_events']) }}</h3>
                        <p>Events chờ duyệt</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon requests">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-content">
                        <h3>{{ number_format($stats['organizer_requests']) }}</h3>
                        <p>Yêu cầu Organizer</p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Pending Events Preview -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Sự kiện chờ duyệt mới nhất</h5>
                            <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($pendingEvents as $event)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $event->title }}</h6>
                                            <small class="text-muted">Bởi: {{ $event->organizer->name }} - {{ $event->created_at->diffForHumans() }}</small>
                                        </div>
                                        <form action="{{ route('admin.events.approve', $event->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted">Không có sự kiện nào chờ duyệt.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Organizer Requests Preview -->
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Yêu cầu nâng cấp mới nhất</h5>
                            <a href="{{ route('admin.requests.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($latestRequests as $req)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $req->name }}</h6>
                                            <small class="text-muted">{{ $req->email }}</small>
                                        </div>
                                        <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-primary">Duyệt</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="p-3 text-center text-muted">Không có yêu cầu nào.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
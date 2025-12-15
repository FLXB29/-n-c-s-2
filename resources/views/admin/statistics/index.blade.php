<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê - Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { padding-top: 80px; }
        .admin-sidebar { top: 80px !important; height: calc(100vh - 80px); }
        .admin-main { margin-top: 0 !important; padding: 20px 30px; }
        .stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 15px; }
        .stat-box .icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-box .icon.blue { background: #e0e7ff; color: #4f46e5; }
        .stat-box .icon.green { background: #d1fae5; color: #059669; }
        .stat-box .icon.orange { background: #ffedd5; color: #ea580c; }
        .stat-box .icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-box .info h3 { font-size: 24px; font-weight: 700; margin: 0; color: #1e293b; }
        .stat-box .info p { margin: 0; color: #64748b; font-size: 14px; }
        .filter-bar { background: #fff; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .filter-bar label { font-weight: 500; color: #475569; }
        .filter-bar input[type="date"] { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .filter-bar button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; }
        .filter-bar .btn-filter { background: #4f46e5; color: #fff; }
        .filter-bar .btn-export { background: #10b981; color: #fff; }
        .chart-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
        .chart-container h4 { margin: 0 0 15px 0; color: #1e293b; }
        .table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
        .table-container h4 { margin: 0 0 15px 0; color: #1e293b; }
        .table-container table { width: 100%; border-collapse: collapse; }
        .table-container th, .table-container td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table-container th { background: #f8fafc; font-weight: 600; color: #475569; }
        .export-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .export-group a { padding: 8px 14px; background: #10b981; color: #fff; border-radius: 6px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; }
        .export-group a:hover { background: #059669; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        @media (max-width: 992px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @include('partials.navbar')
    @include('admin.partials.sidebar')

    <!-- Sidebar -->
    {{-- <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li class="menu-item active">
                    <a href="{{ route('admin.statistics') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Thống kê</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.events.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý Events</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.requests.index') }}">
                        <i class="fas fa-user-clock"></i>
                        <span>Yêu cầu Organizer</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Quản lý Users</span>
                    </a>
                </li>
                <li class="menu-item">
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
        <div class="section-header" style="margin-bottom: 25px;">
            <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 5px;">Thống kê & Báo cáo</h1>
            <p style="color: #64748b; margin: 0;">Xem tổng quan hoạt động và xuất báo cáo</p>
        </div>

        <!-- Filter Bar -->
        <form class="filter-bar" method="GET" action="{{ route('admin.statistics') }}">
            <label>Từ ngày:</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <label>Đến ngày:</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Lọc</button>
        </form>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-box">
                <div class="icon blue"><i class="fas fa-users"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['total_users']) }}</h3>
                    <p>Tổng Users</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-user-plus"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['new_users']) }}</h3>
                    <p>Users mới ({{ $startDate->format('d/m') }} - {{ $endDate->format('d/m') }})</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon purple"><i class="fas fa-calendar-alt"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['total_events']) }}</h3>
                    <p>Tổng Events</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon orange"><i class="fas fa-shopping-cart"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['orders_in_range']) }}</h3>
                    <p>Đơn hàng trong kỳ</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-check-circle"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['paid_orders']) }}</h3>
                    <p>Đơn đã thanh toán</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon blue"><i class="fas fa-money-bill-wave"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['total_revenue']) }} ₫</h3>
                    <p>Doanh thu trong kỳ</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon purple"><i class="fas fa-ticket-alt"></i></div>
                <div class="info">
                    <h3>{{ number_format($stats['tickets_sold']) }}</h3>
                    <p>Vé bán ra trong kỳ</p>
                </div>
            </div>
        </div>

        <!-- Export Buttons -->
        <div class="table-container">
            <h4>Thống kê chi tiết</h4>
            <div class="export-group" style="margin-bottom: 20px;">
                <a href="{{ route('admin.statistics.events') }}" style="background: #4f46e5;">
                    <i class="fas fa-calendar-alt"></i> Thống kê theo sự kiện
                </a>
                <a href="{{ route('admin.statistics.users') }}" style="background: #7c3aed;">
                    <i class="fas fa-users"></i> Thống kê theo người dùng
                </a>
            </div>
            <h4>Xuất báo cáo CSV (mở bằng Excel)</h4>
            <div class="export-group">
                <a href="{{ route('admin.export.orders', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}">
                    <i class="fas fa-file-csv"></i> Xuất Đơn hàng
                </a>
                <a href="{{ route('admin.export.revenue', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}">
                    <i class="fas fa-file-csv"></i> Xuất Doanh thu theo ngày
                </a>
                <a href="{{ route('admin.export.users') }}">
                    <i class="fas fa-file-csv"></i> Xuất Danh sách Users
                </a>
                <a href="{{ route('admin.export.events') }}">
                    <i class="fas fa-file-csv"></i> Xuất Danh sách Events
                </a>
            </div>
        </div>

        <div class="grid-2">
            <!-- Revenue Chart -->
            <div class="chart-container">
                <h4>Doanh thu theo ngày</h4>
                <canvas id="revenueChart" height="220"></canvas>
            </div>

            <!-- Orders by Status -->
            <div class="chart-container">
                <h4>Đơn hàng theo trạng thái</h4>
                <canvas id="ordersChart" height="220"></canvas>
            </div>
        </div>

        <!-- Top Events -->
        <div class="table-container">
            <h4 style="display: flex; justify-content: space-between; align-items: center;">
                Top 10 sự kiện doanh thu cao nhất
                <a href="{{ route('admin.statistics.events') }}" style="font-size: 13px; color: #4f46e5; text-decoration: none; font-weight: 500;">
                    Xem tất cả <i class="fas fa-arrow-right"></i>
                </a>
            </h4>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sự kiện</th>
                        <th>Số đơn</th>
                        <th>Doanh thu</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topEvents as $index => $event)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->order_count }}</td>
                            <td>{{ number_format($event->revenue) }} ₫</td>
                            <td>
                                <a href="{{ route('admin.statistics.event.detail', $event->id) }}" style="color: #4f46e5; text-decoration: none;">
                                    <i class="fas fa-chart-bar"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: #64748b;">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Users by Role -->
        <div class="grid-2">
            <div class="chart-container">
                <h4>Người dùng theo vai trò</h4>
                <canvas id="usersChart" height="180"></canvas>
            </div>
        </div>
    </main>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                //trục hoành
                labels: {!! json_encode($revenueByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    //trục tung
                    data: {!! json_encode($revenueByDay->pluck('revenue')) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Orders by Status Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($ordersByStatus->toArray())) !!},
                datasets: [{
                    data: {!! json_encode(array_values($ordersByStatus->toArray())) !!},
                    backgroundColor: ['#fbbf24', '#10b981', '#ef4444', '#6366f1', '#8b5cf6']
                }]
            },
            options: { responsive: true }
        });

        // Users by Role Chart
        const usersCtx = document.getElementById('usersChart').getContext('2d');
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($usersByRole->toArray())) !!},
                datasets: [{
                    label: 'Số lượng',
                    data: {!! json_encode(array_values($usersByRole->toArray())) !!},
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
</body>
</html>

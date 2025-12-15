<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết thống kê: {{ $user->full_name }} - Admin Dashboard</title>
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
        
        .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; }
        .breadcrumb a { color: #4f46e5; text-decoration: none; }
        .breadcrumb span { color: #64748b; }

        .user-header { background: linear-gradient(135deg, #10b981, #059669); color: #fff; padding: 25px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 20px; }
        .user-header .avatar { width: 80px; height: 80px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 32px; overflow: hidden; }
        .user-header .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-header .info h1 { margin: 0 0 8px 0; font-size: 22px; }
        .user-header .info .meta { display: flex; gap: 20px; flex-wrap: wrap; font-size: 14px; opacity: 0.9; }
        .user-header .info .meta span { display: flex; align-items: center; gap: 6px; }

        .stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .stat-box .icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
        .stat-box .icon.blue { background: #e0e7ff; color: #4f46e5; }
        .stat-box .icon.green { background: #d1fae5; color: #059669; }
        .stat-box .icon.orange { background: #ffedd5; color: #ea580c; }
        .stat-box .icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-box .icon.red { background: #fee2e2; color: #dc2626; }
        .stat-box h3 { font-size: 20px; font-weight: 700; margin: 0 0 5px 0; color: #1e293b; }
        .stat-box p { margin: 0; color: #64748b; font-size: 13px; }

        .filter-bar { background: #fff; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .filter-bar label { font-weight: 500; color: #475569; }
        .filter-bar input[type="date"] { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .filter-bar button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; }
        .filter-bar .btn-filter { background: #4f46e5; color: #fff; }
        .filter-bar .btn-export { background: #10b981; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        @media (max-width: 1200px) { .grid-3 { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 992px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }

        .chart-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .chart-container h4 { margin: 0 0 15px 0; color: #1e293b; font-size: 16px; }

        .table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
        .table-container h4 { margin: 0 0 15px 0; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .table-container h4 .badge { background: #10b981; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .table-container table { width: 100%; border-collapse: collapse; }
        .table-container th, .table-container td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table-container th { background: #f8fafc; font-weight: 600; color: #475569; }

        .category-card { background: #f8fafc; border-radius: 10px; padding: 15px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; }
        .category-card.favorite { background: #fef3c7; border-left: 4px solid #f59e0b; }
        .category-card .name { font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .category-card .name .star { color: #f59e0b; }
        .category-card .stats { display: flex; gap: 20px; }
        .category-card .stats .stat { text-align: center; }
        .category-card .stats .stat .value { font-weight: 700; color: #1e293b; }
        .category-card .stats .stat .label { font-size: 12px; color: #64748b; }

        .ticket-type-tag { display: inline-block; padding: 4px 10px; background: #e0e7ff; color: #4f46e5; border-radius: 20px; font-size: 12px; margin: 2px; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    @include('partials.navbar')
    @include('admin.partials.sidebar')

    <main class="admin-main">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('admin.statistics') }}">Thống kê</a>
            <span>/</span>
            <a href="{{ route('admin.statistics.users') }}">Theo người dùng</a>
            <span>/</span>
            <span>{{ Str::limit($user->full_name, 25) }}</span>
        </div>

        <!-- User Header -->
        <div class="user-header">
            <div class="avatar">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="">
                @else
                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                @endif
            </div>
            <div class="info">
                <h1>{{ $user->full_name }}</h1>
                <div class="meta">
                    <span><i class="fas fa-envelope"></i> {{ $user->email }}</span>
                    <span><i class="fas fa-phone"></i> {{ $user->phone ?? 'N/A' }}</span>
                    <span><i class="fas fa-user-tag"></i> {{ ucfirst($user->role) }}</span>
                    <span><i class="fas fa-calendar-plus"></i> Tham gia: {{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <form class="filter-bar" method="GET" action="{{ route('admin.statistics.user.detail', $user->id) }}">
            <label>Từ ngày:</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <label>Đến ngày:</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Lọc</button>
            <a href="{{ route('admin.export.user.detail', ['user' => $user->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn-export">
                <i class="fas fa-file-csv"></i> Xuất CSV
            </a>
        </form>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-money-bill-wave"></i></div>
                <h3>{{ number_format($overview['total_spent']) }} ₫</h3>
                <p>Tổng chi tiêu</p>
            </div>
            <div class="stat-box">
                <div class="icon blue"><i class="fas fa-shopping-cart"></i></div>
                <h3>{{ number_format($overview['total_orders']) }}</h3>
                <p>Đơn hàng</p>
            </div>
            <div class="stat-box">
                <div class="icon orange"><i class="fas fa-ticket-alt"></i></div>
                <h3>{{ number_format($overview['total_tickets']) }}</h3>
                <p>Vé đã mua</p>
            </div>
            <div class="stat-box">
                <div class="icon purple"><i class="fas fa-calculator"></i></div>
                <h3>{{ number_format($overview['avg_order_value']) }} ₫</h3>
                <p>Giá trị TB/đơn</p>
            </div>
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-check-circle"></i></div>
                <h3>{{ number_format($overview['tickets_used']) }}</h3>
                <p>Vé đã sử dụng</p>
            </div>
            <div class="stat-box">
                <div class="icon red"><i class="fas fa-clock"></i></div>
                <h3>{{ number_format($overview['pending_orders']) }}</h3>
                <p>Đơn chờ xử lý</p>
            </div>
        </div>

        <!-- Category Stats & Spending Chart -->
        <div class="grid-2">
            <!-- Favorite Categories -->
            <div class="table-container">
                <h4>
                    <i class="fas fa-heart"></i> Loại sự kiện hay mua
                    @if($favoriteCategory)
                        <span class="badge">⭐ Yêu thích: {{ $favoriteCategory->name }}</span>
                    @endif
                </h4>
                
                @forelse($categoryStats as $category)
                    @php
                        $isFavorite = $favoriteCategory && $category->id === $favoriteCategory->id;
                    @endphp
                    <div class="category-card {{ $isFavorite ? 'favorite' : '' }}">
                        <div class="name">
                            @if($isFavorite)
                                <i class="fas fa-star star"></i>
                            @endif
                            {{ $category->name }}
                        </div>
                        <div class="stats">
                            <div class="stat">
                                <div class="value">{{ number_format($category->order_count) }}</div>
                                <div class="label">Đơn hàng</div>
                            </div>
                            <div class="stat">
                                <div class="value">{{ number_format($category->total_spent) }} ₫</div>
                                <div class="label">Chi tiêu</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #64748b; text-align: center; padding: 20px;">Chưa có dữ liệu</p>
                @endforelse
            </div>

            <!-- Spending by Month Chart -->
            <div class="chart-container">
                <h4><i class="fas fa-chart-line"></i> Chi tiêu theo tháng</h4>
                <canvas id="spendingChart" height="220"></canvas>
            </div>
        </div>

        <!-- Ticket Types -->
        <div class="grid-2">
            <div class="table-container">
                <h4><i class="fas fa-ticket-alt"></i> Loại vé hay mua</h4>
                @forelse($ticketTypeStats as $ticketType)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0;">
                        <span class="ticket-type-tag">{{ $ticketType->name }}</span>
                        <div style="text-align: right;">
                            <strong>{{ number_format($ticketType->count) }} vé</strong>
                            <br><small style="color: #64748b;">{{ number_format($ticketType->total) }} ₫</small>
                        </div>
                    </div>
                @empty
                    <p style="color: #64748b; text-align: center; padding: 20px;">Chưa có dữ liệu</p>
                @endforelse
            </div>

            <!-- Category Distribution Chart -->
            <div class="chart-container">
                <h4><i class="fas fa-chart-pie"></i> Phân bổ theo loại sự kiện</h4>
                <canvas id="categoryChart" height="220"></canvas>
            </div>
        </div>

        <!-- Attended Events -->
        <div class="table-container">
            <h4><i class="fas fa-calendar-check"></i> Sự kiện đã tham gia ({{ $attendedEvents->count() }} sự kiện)</h4>
            <table>
                <thead>
                    <tr>
                        <th>Sự kiện</th>
                        <th>Ngày tổ chức</th>
                        <th>Số đơn</th>
                        <th>Chi tiêu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendedEvents as $event)
                        <tr>
                            <td>
                                <strong>{{ Str::limit($event->title, 40) }}</strong>
                                <br><small style="color: #64748b;">{{ $event->category->name ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $event->start_datetime ? $event->start_datetime->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>{{ number_format($event->order_count) }}</td>
                            <td><strong>{{ number_format($event->amount_spent) }} ₫</strong></td>
                            <td>
                                <a href="{{ route('admin.statistics.event.detail', $event->id) }}" style="color: #4f46e5; text-decoration: none;">
                                    <i class="fas fa-chart-bar"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align: center; color: #64748b;">Chưa tham gia sự kiện nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Recent Orders -->
        <div class="table-container">
            <h4><i class="fas fa-shopping-cart"></i> Đơn hàng gần đây</h4>
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Sự kiện</th>
                        <th>Số vé</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>{{ $order->order_code }}</strong></td>
                            <td>{{ Str::limit($order->event->title ?? 'N/A', 30) }}</td>
                            <td>
                                {{ $order->tickets->count() }}
                                @if($order->tickets->count() > 0)
                                    <br>
                                    @foreach($order->tickets->take(2) as $ticket)
                                        <span class="ticket-type-tag" style="font-size: 10px; padding: 2px 6px;">
                                            {{ $ticket->ticketType->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                    @if($order->tickets->count() > 2)
                                        <span style="color: #64748b; font-size: 10px;">+{{ $order->tickets->count() - 2 }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>{{ number_format($order->final_amount) }} ₫</td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: #64748b;">Chưa có đơn hàng</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>

    <script>
        // Spending by Month Chart
        const spendingCtx = document.getElementById('spendingChart').getContext('2d');
        new Chart(spendingCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($spendingByMonth->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('m/Y'))) !!},
                datasets: [{
                    label: 'Chi tiêu (VNĐ)',
                    data: {!! json_encode($spendingByMonth->pluck('spent')) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
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

        // Category Distribution Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryStats->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($categoryStats->pluck('total_spent')) !!},
                    backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    </script>
</body>
</html>

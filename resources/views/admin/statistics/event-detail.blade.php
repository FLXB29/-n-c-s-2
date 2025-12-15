<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết thống kê: {{ $event->title }} - Admin Dashboard</title>
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

        .event-header { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; padding: 25px; border-radius: 12px; margin-bottom: 25px; }
        .event-header h1 { margin: 0 0 10px 0; font-size: 22px; }
        .event-header .meta { display: flex; gap: 20px; flex-wrap: wrap; font-size: 14px; opacity: 0.9; }
        .event-header .meta span { display: flex; align-items: center; gap: 6px; }

        .stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .stat-box .icon { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
        .stat-box .icon.blue { background: #e0e7ff; color: #4f46e5; }
        .stat-box .icon.green { background: #d1fae5; color: #059669; }
        .stat-box .icon.orange { background: #ffedd5; color: #ea580c; }
        .stat-box .icon.purple { background: #ede9fe; color: #7c3aed; }
        .stat-box h3 { font-size: 22px; font-weight: 700; margin: 0 0 5px 0; color: #1e293b; }
        .stat-box p { margin: 0; color: #64748b; font-size: 13px; }

        .filter-bar { background: #fff; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .filter-bar label { font-weight: 500; color: #475569; }
        .filter-bar input[type="date"] { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .filter-bar button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; }
        .filter-bar .btn-filter { background: #4f46e5; color: #fff; }
        .filter-bar .btn-export { background: #10b981; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        @media (max-width: 992px) { .grid-2 { grid-template-columns: 1fr; } }

        .chart-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .chart-container h4 { margin: 0 0 15px 0; color: #1e293b; font-size: 16px; }

        .table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 25px; }
        .table-container h4 { margin: 0 0 15px 0; color: #1e293b; display: flex; align-items: center; gap: 10px; }
        .table-container h4 .badge { background: #4f46e5; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .table-container table { width: 100%; border-collapse: collapse; }
        .table-container th, .table-container td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table-container th { background: #f8fafc; font-weight: 600; color: #475569; }

        .ticket-type-card { background: #f8fafc; border-radius: 10px; padding: 15px; margin-bottom: 12px; border-left: 4px solid #4f46e5; }
        .ticket-type-card.best-seller { border-left-color: #10b981; background: #f0fdf4; }
        .ticket-type-card .name { font-weight: 600; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .ticket-type-card .name .crown { color: #f59e0b; }
        .ticket-type-card .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .ticket-type-card .stats .stat { text-align: center; }
        .ticket-type-card .stats .stat .value { font-size: 18px; font-weight: 700; color: #1e293b; }
        .ticket-type-card .stats .stat .label { font-size: 12px; color: #64748b; }
        .ticket-type-card .progress-bar { height: 6px; background: #e2e8f0; border-radius: 3px; margin-top: 10px; overflow: hidden; }
        .ticket-type-card .progress-bar .fill { height: 100%; background: #4f46e5; border-radius: 3px; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-paid { background: #d1fae5; color: #059669; }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        .status-active { background: #dbeafe; color: #2563eb; }
        .status-used { background: #e0e7ff; color: #4f46e5; }

        .user-avatar { width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 14px; color: #64748b; }
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
            <a href="{{ route('admin.statistics.events') }}">Theo sự kiện</a>
            <span>/</span>
            <span>{{ Str::limit($event->title, 30) }}</span>
        </div>

        <!-- Event Header -->
        <div class="event-header">
            <h1>{{ $event->title }}</h1>
            <div class="meta">
                <span><i class="fas fa-calendar"></i> {{ $event->start_datetime ? $event->start_datetime->format('d/m/Y H:i') : 'N/A' }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $event->venue_name }}</span>
                <span><i class="fas fa-user"></i> {{ $event->organizer->full_name ?? 'N/A' }}</span>
                <span><i class="fas fa-tag"></i> {{ $event->category->name ?? 'N/A' }}</span>
            </div>
        </div>

        <!-- Filter Bar -->
        <form class="filter-bar" method="GET" action="{{ route('admin.statistics.event.detail', $event->id) }}">
            <label>Từ ngày:</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            <label>Đến ngày:</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Lọc</button>
            <a href="{{ route('admin.export.event.detail', ['event' => $event->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" class="btn-export">
                <i class="fas fa-file-csv"></i> Xuất CSV
            </a>
        </form>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-money-bill-wave"></i></div>
                <h3>{{ number_format($overview['total_revenue']) }} ₫</h3>
                <p>Tổng doanh thu</p>
            </div>
            <div class="stat-box">
                <div class="icon blue"><i class="fas fa-shopping-cart"></i></div>
                <h3>{{ number_format($overview['total_orders']) }}</h3>
                <p>Đơn hàng</p>
            </div>
            <div class="stat-box">
                <div class="icon orange"><i class="fas fa-ticket-alt"></i></div>
                <h3>{{ number_format($overview['total_tickets']) }}</h3>
                <p>Vé bán ra</p>
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
        </div>

        <!-- Ticket Types -->
        <div class="table-container">
            <h4>
                <i class="fas fa-ticket-alt"></i> Thống kê theo loại vé
                @if($bestSellingTicketType)
                    <span class="badge">🏆 Bán chạy: {{ $bestSellingTicketType->name }}</span>
                @endif
            </h4>
            
            @forelse($ticketTypeStats as $ticketType)
                @php
                    $isBestSeller = $bestSellingTicketType && $ticketType->id === $bestSellingTicketType->id;
                    $soldPercent = $ticketType->quantity > 0 ? ($ticketType->tickets_sold_count / $ticketType->quantity) * 100 : 0;
                @endphp
                <div class="ticket-type-card {{ $isBestSeller ? 'best-seller' : '' }}">
                    <div class="name">
                        @if($isBestSeller)
                            <i class="fas fa-crown crown"></i>
                        @endif
                        {{ $ticketType->name }}
                        <span style="color: #64748b; font-weight: 400; font-size: 13px;">- {{ number_format($ticketType->price) }} ₫/vé</span>
                    </div>
                    <div class="stats">
                        <div class="stat">
                            <div class="value">{{ number_format($ticketType->tickets_sold_count) }}</div>
                            <div class="label">Vé đã bán</div>
                        </div>
                        <div class="stat">
                            <div class="value">{{ number_format($ticketType->quantity) }}</div>
                            <div class="label">Tổng số vé</div>
                        </div>
                        <div class="stat">
                            <div class="value">{{ number_format($ticketType->revenue) }} ₫</div>
                            <div class="label">Doanh thu</div>
                        </div>
                    </div>
                    <div class="progress-bar">
                        <div class="fill" style="width: {{ min($soldPercent, 100) }}%;"></div>
                    </div>
                </div>
            @empty
                <p style="color: #64748b; text-align: center; padding: 20px;">Chưa có loại vé nào</p>
            @endforelse
        </div>

        <!-- Charts -->
        <div class="grid-2">
            <div class="chart-container">
                <h4><i class="fas fa-chart-line"></i> Doanh thu theo ngày</h4>
                <canvas id="revenueChart" height="200"></canvas>
            </div>
            <div class="chart-container">
                <h4><i class="fas fa-chart-bar"></i> Vé bán theo ngày</h4>
                <canvas id="ticketsChart" height="200"></canvas>
            </div>
        </div>

        <!-- Top Buyers -->
        <div class="table-container">
            <h4><i class="fas fa-users"></i> Top 10 khách hàng mua nhiều nhất</h4>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Số đơn</th>
                        <th>Tổng chi tiêu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topBuyers as $index => $buyer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div class="user-avatar">
                                        @if($buyer->avatar)
                                            <img src="{{ Storage::url($buyer->avatar) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                        @else
                                            {{ strtoupper(substr($buyer->full_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    {{ $buyer->full_name }}
                                </div>
                            </td>
                            <td>{{ $buyer->email }}</td>
                            <td>{{ number_format($buyer->order_count) }}</td>
                            <td><strong>{{ number_format($buyer->total_spent) }} ₫</strong></td>
                            <td>
                                <a href="{{ route('admin.statistics.user.detail', $buyer->id) }}" style="color: #4f46e5; text-decoration: none;">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center; color: #64748b;">Chưa có dữ liệu</td></tr>
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
                        <th>Khách hàng</th>
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
                            <td>{{ $order->user->full_name ?? 'N/A' }}</td>
                            <td>{{ $order->tickets->count() }}</td>
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
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
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

        // Tickets Chart
        const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
        new Chart(ticketsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($ticketsByDay->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
                datasets: [{
                    label: 'Số vé',
                    data: {!! json_encode($ticketsByDay->pluck('count')) !!},
                    backgroundColor: '#10b981'
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

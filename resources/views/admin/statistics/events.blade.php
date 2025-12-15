<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê theo sự kiện - Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .stat-box .info h3 { font-size: 24px; font-weight: 700; margin: 0; color: #1e293b; }
        .stat-box .info p { margin: 0; color: #64748b; font-size: 14px; }

        .breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; }
        .breadcrumb a { color: #4f46e5; text-decoration: none; }
        .breadcrumb span { color: #64748b; }

        .filter-bar { background: #fff; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .filter-bar input, .filter-bar select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .filter-bar input[type="text"] { min-width: 250px; }
        .filter-bar button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; background: #4f46e5; color: #fff; }

        .table-container { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .table-container h4 { margin: 0 0 15px 0; color: #1e293b; }
        .table-container table { width: 100%; border-collapse: collapse; }
        .table-container th, .table-container td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .table-container th { background: #f8fafc; font-weight: 600; color: #475569; }
        .table-container th a { color: #475569; text-decoration: none; display: flex; align-items: center; gap: 5px; }
        .table-container th a:hover { color: #4f46e5; }
        .table-container tr:hover { background: #f8fafc; }

        .btn-view { padding: 6px 12px; background: #4f46e5; color: #fff; border-radius: 6px; text-decoration: none; font-size: 13px; }
        .btn-view:hover { background: #4338ca; }

        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .status-published { background: #d1fae5; color: #059669; }
        .status-draft { background: #e2e8f0; color: #64748b; }
        .status-cancelled { background: #fee2e2; color: #dc2626; }
        .status-pending { background: #fef3c7; color: #d97706; }

        .pagination-wrapper { margin-top: 20px; display: flex; justify-content: center; }
        .pagination { display: flex; gap: 5px; list-style: none; padding: 0; }
        .pagination li a, .pagination li span { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; color: #475569; }
        .pagination li.active span { background: #4f46e5; color: #fff; border-color: #4f46e5; }
        .pagination li a:hover { background: #f1f5f9; }

        .sort-icon { font-size: 10px; margin-left: 4px; }
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
            <span>Theo sự kiện</span>
        </div>

        <div class="section-header" style="margin-bottom: 25px;">
            <h1 style="font-size: 24px; color: #1e293b; margin-bottom: 5px;">Thống kê theo sự kiện</h1>
            <p style="color: #64748b; margin: 0;">Xem doanh thu và hiệu suất của từng sự kiện</p>
        </div>

        <!-- Stats Overview -->
        <div class="stats-overview">
            <div class="stat-box">
                <div class="icon green"><i class="fas fa-money-bill-wave"></i></div>
                <div class="info">
                    <h3>{{ number_format($totalRevenue) }} ₫</h3>
                    <p>Tổng doanh thu</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon blue"><i class="fas fa-shopping-cart"></i></div>
                <div class="info">
                    <h3>{{ number_format($totalOrders) }}</h3>
                    <p>Tổng đơn hàng</p>
                </div>
            </div>
            <div class="stat-box">
                <div class="icon orange"><i class="fas fa-ticket-alt"></i></div>
                <div class="info">
                    <h3>{{ number_format($totalTickets) }}</h3>
                    <p>Tổng vé bán ra</p>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <form class="filter-bar" method="GET" action="{{ route('admin.statistics.events') }}">
            <input type="text" name="search" placeholder="Tìm kiếm sự kiện..." value="{{ $search ?? '' }}">
            <select name="sort">
                <option value="revenue" {{ $sortBy == 'revenue' ? 'selected' : '' }}>Sắp xếp theo doanh thu</option>
                <option value="orders" {{ $sortBy == 'orders' ? 'selected' : '' }}>Sắp xếp theo số đơn</option>
                <option value="tickets" {{ $sortBy == 'tickets' ? 'selected' : '' }}>Sắp xếp theo số vé</option>
                <option value="date" {{ $sortBy == 'date' ? 'selected' : '' }}>Sắp xếp theo ngày</option>
            </select>
            <select name="order">
                <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Tăng dần</option>
            </select>
            <button type="submit"><i class="fas fa-filter"></i> Lọc</button>
        </form>

        <!-- Events Table -->
        <div class="table-container">
            <h4>Danh sách sự kiện ({{ $events->total() }} sự kiện)</h4>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sự kiện</th>
                        <th>Ngày tổ chức</th>
                        <th>Trạng thái</th>
                        <th>
                            <a href="{{ route('admin.statistics.events', ['sort' => 'orders', 'order' => ($sortBy == 'orders' && $sortOrder == 'desc') ? 'asc' : 'desc', 'search' => $search]) }}">
                                Số đơn
                                @if($sortBy == 'orders')
                                    <i class="fas fa-sort-{{ $sortOrder == 'desc' ? 'down' : 'up' }} sort-icon"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.statistics.events', ['sort' => 'tickets', 'order' => ($sortBy == 'tickets' && $sortOrder == 'desc') ? 'asc' : 'desc', 'search' => $search]) }}">
                                Vé bán
                                @if($sortBy == 'tickets')
                                    <i class="fas fa-sort-{{ $sortOrder == 'desc' ? 'down' : 'up' }} sort-icon"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('admin.statistics.events', ['sort' => 'revenue', 'order' => ($sortBy == 'revenue' && $sortOrder == 'desc') ? 'asc' : 'desc', 'search' => $search]) }}">
                                Doanh thu
                                @if($sortBy == 'revenue')
                                    <i class="fas fa-sort-{{ $sortOrder == 'desc' ? 'down' : 'up' }} sort-icon"></i>
                                @endif
                            </a>
                        </th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $index => $event)
                        <tr>
                            <td>{{ $events->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ Str::limit($event->title, 40) }}</strong>
                                <br><small style="color: #64748b;">{{ $event->organizer->full_name ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $event->start_datetime ? $event->start_datetime->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td>
                                <span class="status-badge status-{{ $event->status }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td>{{ number_format($event->total_orders) }}</td>
                            <td>{{ number_format($event->total_tickets_sold) }}</td>
                            <td><strong>{{ number_format($event->total_revenue) }} ₫</strong></td>
                            <td>
                                <a href="{{ route('admin.statistics.event.detail', $event->id) }}" class="btn-view">
                                    <i class="fas fa-chart-bar"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: #64748b; padding: 40px;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                                Không tìm thấy sự kiện nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $events->appends(['search' => $search, 'sort' => $sortBy, 'order' => $sortOrder])->links() }}
            </div>
        </div>
    </main>
</body>
</html>

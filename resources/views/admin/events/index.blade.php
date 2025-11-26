<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Events - Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding-top: 80px; }
        .admin-sidebar { top: 80px !important; height: calc(100vh - 80px); }
        .admin-main { margin-top: 0 !important; padding-top: 20px; }
    </style>
</head>
<body>
    @include('partials.navbar')

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-content">
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
                <li class="menu-item active">
                    <a href="{{ route('admin.events.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý Events</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.requests.index') }}">
                        <i class="fas fa-user-clock"></i>
                        <span>Yêu cầu Organizer</span>
                        @php
                            $pendingRequestsCount = \App\Models\User::where('organizer_request_status', 'pending')->count();
                        @endphp
                        @if($pendingRequestsCount > 0)
                            <span class="badge bg-danger fw-bold rounded-pill ms-auto" style="background-color: #dc3545 !important; color: white !important;">{{ $pendingRequestsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Quản lý Users</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="admin-main">
        <section class="dashboard-section active">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Quản lý Sự kiện</h1>
                    <p>Duyệt và quản lý các sự kiện trên hệ thống</p>
                </div>
                <div>
                    <a href="{{ route('admin.events.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                    <a href="{{ route('admin.events.index', ['status' => 'pending']) }}" class="btn {{ request('status') == 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Chờ duyệt</a>
                    <a href="{{ route('admin.events.index', ['status' => 'published']) }}" class="btn {{ request('status') == 'published' ? 'btn-success' : 'btn-outline-success' }}">Đã xuất bản</a>
                    <a href="{{ route('admin.events.index', ['status' => 'suspended']) }}" class="btn {{ request('status') == 'suspended' ? 'btn-secondary' : 'btn-outline-secondary' }}">Đã tạm dừng</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="admin-card table-card">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sự kiện</th>
                                <th>Organizer</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $event->featured_image ? asset($event->featured_image) : asset('images/event-placeholder.jpg') }}" 
                                             alt="" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                        <div>
                                            <div class="fw-bold">{{ $event->title }}</div>
                                            <small class="text-muted">{{ $event->category->name ?? 'Uncategorized' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $event->organizer->name }}</div>
                                    <small class="text-muted">{{ $event->organizer->email }}</small>
                                </td>
                                <td>
                                    <div>{{ $event->start_datetime->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $event->start_datetime->format('H:i') }}</small>
                                </td>
                                <td>
                                    @if($event->status == 'published')
                                        <span class="badge bg-success">Đã xuất bản</span>
                                    @elseif($event->status == 'pending')
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @elseif($event->status == 'suspended')
                                        <span class="badge bg-secondary">Đã tạm dừng</span>
                                    @elseif($event->status == 'draft')
                                        <span class="badge bg-secondary">Nháp</span>
                                    @else
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('events.show', $event->id) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($event->status == 'pending')
                                            <form action="{{ route('admin.events.approve', $event->id) }}" method="POST" onsubmit="return confirm('Duyệt sự kiện này?');">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-success" title="Duyệt">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.events.reject', $event->id) }}" method="POST" onsubmit="return confirm('Từ chối sự kiện này?');">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-danger" title="Từ chối">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @elseif($event->status == 'published')
                                            <form action="{{ route('admin.events.suspend', $event->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn tạm dừng sự kiện này?');">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-warning text-white" title="Tạm dừng">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @elseif($event->status == 'suspended')
                                            <form action="{{ route('admin.events.restore', $event->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn khôi phục sự kiện này?');">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn-sm btn-success" title="Khôi phục">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Không tìm thấy sự kiện nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $events->links() }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>
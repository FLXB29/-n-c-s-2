<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu Organizer - Admin Dashboard</title>
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
                <li class="menu-item">
                    <a href="{{ route('admin.events.index') }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý Events</span>
                    </a>
                </li>
                <li class="menu-item active">
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
            <div class="section-header">
                <h1>Yêu cầu nâng cấp Organizer</h1>
                <p>Duyệt các yêu cầu trở thành nhà tổ chức từ người dùng</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="admin-card table-card">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Thông tin liên hệ</th>
                                <th>Thời gian yêu cầu</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $req->avatar ? asset($req->avatar) : asset('images/default-avatar.png') }}" 
                                             alt="" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold">{{ $req->name }}</div>
                                            <small class="text-muted">ID: {{ $req->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $req->email }}</div>
                                    <small class="text-muted">{{ $req->phone ?? 'Chưa cập nhật SĐT' }}</small>
                                </td>
                                <td>
                                    <div>{{ $req->organizer_request_at ? \Carbon\Carbon::parse($req->organizer_request_at)->format('d/m/Y H:i') : 'N/A' }}</div>
                                    <small class="text-muted">{{ $req->organizer_request_at ? \Carbon\Carbon::parse($req->organizer_request_at)->diffForHumans() : '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.requests.approve', $req->id) }}" method="POST" onsubmit="return confirm('Chấp nhận yêu cầu này? Người dùng sẽ trở thành Organizer.');">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-success" title="Chấp nhận">
                                                <i class="fas fa-check"></i> Chấp nhận
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST" onsubmit="return confirm('Từ chối yêu cầu này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" title="Từ chối">
                                                <i class="fas fa-times"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Không có yêu cầu nào đang chờ xử lý.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $requests->links() }}
                </div>
            </div>
        </section>
    </main>
</body>
</html>
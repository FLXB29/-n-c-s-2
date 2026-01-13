<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Users - Admin Dashboard</title>
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
                        @php
                            $pendingRequestsCount = \App\Models\User::where('organizer_request_status', 'pending')->count();
                        @endphp
                        @if($pendingRequestsCount > 0)
                            <span class="badge bg-danger fw-bold rounded-pill ms-auto" style="background-color: #dc3545 !important; color: white !important;">{{ $pendingRequestsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="menu-item active">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>Quản lý Users</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside> --}}

    <main class="admin-main">
        <section class="dashboard-section active">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Quản lý Người dùng</h1>
                    <p>Quản lý tài khoản và phân quyền người dùng</p>
                </div>
                <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex align-items-center bg-white p-1 rounded shadow-sm border">
                    <div class="d-flex flex-grow-1">
                        <div class="input-group border-end">
                            <span class="input-group-text bg-transparent border-0"><i class="fas fa-filter text-muted"></i></span>
                            <select name="role" class="form-select border-0 shadow-none" onchange="this.form.submit()" style="width: auto; min-width: 130px;">
                                <option value="">Tất cả vai trò</option>
                                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                                <option value="organizer" {{ request('role') == 'organizer' ? 'selected' : '' }}>Organizer</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="input-group flex-grow-1">
                            <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Tìm tên hoặc email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary m-1 rounded"><i class="fas fa-search"></i></button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="admin-card table-card">
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tham gia</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $user->avatar ? asset($user->avatar) : asset('images/default-avatar.png') }}" 
                                             alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                        <div>
                                            <div class="fw-bold">{{ $user->full_name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge bg-danger" style="color: red">Admin</span>
                                    @elseif($user->role == 'organizer')
                                        <span class="badge bg-info text-dark" style="color: yellow">Organizer</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == 'active')
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger">Bị khóa</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    @if($user->role !== 'admin')
                                        <div class="d-flex justify-content-end gap-2">
                                            <form action="{{ route('admin.toggleRole', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn thay đổi quyền của user này?');">
                                                @csrf
                                                @method('PATCH')
                                                @if($user->role == 'user')
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Thăng cấp lên Organizer">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Hạ cấp xuống User">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                @endif
                                            </form>

                                            <form action="{{ route('admin.toggleStatus', $user->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái của user này?');">
                                                @csrf
                                                @method('PATCH')
                                                @if($user->status == 'active')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Khóa tài khoản">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @else
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Mở khóa tài khoản">
                                                        <i class="fas fa-lock-open"></i>
                                                    </button>
                                                @endif
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Không tìm thấy người dùng nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($users->hasPages())
                <div class="pagination-container p-3">
                    {{ $users->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </section>
    </main>
</body>
</html>
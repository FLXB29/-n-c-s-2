@extends('layouts.app')

@section('title', 'Organizer Dashboard')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Organizer Dashboard</h1>
        <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo sự kiện mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sự kiện của tôi</h5>
        </div>
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sự kiện</th>
                                <th>Thời gian</th>
                                <th>Địa điểm</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <img src="{{ asset($event->featured_image) }}" alt="{{ $event->title }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y H:i') }}</td>
                                    <td>{{ $event->venue_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $event->status == 'published' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-info" target="_blank" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('organizer.events.edit', $event->id) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('organizer.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sự kiện này? Hành động này không thể hoàn tác.')" title="Xóa"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $events->links() }}
                </div>
            @else
                <p class="text-center text-muted my-5">Bạn chưa tạo sự kiện nào.</p>
                <div class="text-center">
                    <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">Tạo sự kiện ngay</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

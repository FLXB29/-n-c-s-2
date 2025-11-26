@extends('layouts.app')

@section('title', 'Chỉnh sửa sự kiện')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Chỉnh sửa sự kiện: {{ $event->title }}</h4>
                    <a href="{{ route('organizer.events.setup', $event->id) }}" class="btn btn-sm btn-outline-dark">
                        <i class="fas fa-chair"></i> Thiết lập Vé & Ghế
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('organizer.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả chi tiết <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_datetime" class="form-label">Thời gian bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_datetime') is-invalid @enderror" id="start_datetime" name="start_datetime" value="{{ old('start_datetime', $event->start_datetime->format('Y-m-d\TH:i')) }}" required>
                                @error('start_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_datetime" class="form-label">Thời gian kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('end_datetime') is-invalid @enderror" id="end_datetime" name="end_datetime" value="{{ old('end_datetime', $event->end_datetime->format('Y-m-d\TH:i')) }}" required>
                                @error('end_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="venue_name" class="form-label">Tên địa điểm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('venue_name') is-invalid @enderror" id="venue_name" name="venue_name" value="{{ old('venue_name', $event->venue_name) }}" placeholder="Ví dụ: Trung tâm Hội nghị Quốc gia" required>
                            @error('venue_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="venue_address" class="form-label">Địa chỉ cụ thể <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('venue_address') is-invalid @enderror" id="venue_address" name="venue_address" value="{{ old('venue_address', $event->venue_address) }}" required>
                                @error('venue_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="venue_city" class="form-label">Thành phố <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('venue_city') is-invalid @enderror" id="venue_city" name="venue_city" value="{{ old('venue_city', $event->venue_city) }}" required>
                                @error('venue_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="min_price" class="form-label">Giá vé thấp nhất (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('min_price') is-invalid @enderror" id="min_price" name="min_price" value="{{ old('min_price', $event->min_price) }}" min="0" required>
                                @error('min_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="max_price" class="form-label">Giá vé cao nhất (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('max_price') is-invalid @enderror" id="max_price" name="max_price" value="{{ old('max_price', $event->max_price) }}" min="0" required>
                                @error('max_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="featured_image" class="form-label">Hình ảnh sự kiện (Để trống nếu không thay đổi)</label>
                            <input type="file" class="form-control @error('featured_image') is-invalid @enderror" id="featured_image" name="featured_image" accept="image/*">
                            @if($event->featured_image)
                                <div class="mt-2">
                                    <p class="text-muted mb-1">Ảnh hiện tại:</p>
                                    <img src="{{ Str::startsWith($event->featured_image, 'http') ? $event->featured_image : asset($event->featured_image) }}" alt="Current Image" style="height: 100px; border-radius: 5px;">
                                </div>
                            @endif
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('organizer.dashboard') }}" class="btn btn-secondary">Hủy bỏ</a>
                            <button type="submit" class="btn btn-warning px-5">Cập nhật sự kiện</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
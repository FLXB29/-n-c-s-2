@extends('layouts.app')

@section('title', 'Danh sách sự kiện')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <!-- <style>
        /* --- CSS BỔ SUNG CHO MOBILE & RESPONSIVE --- */
        
        /* 1. Nút mở bộ lọc trên Mobile */
        .mobile-filter-trigger {
            display: none; /* Ẩn trên máy tính */
            width: 100%;
            padding: 12px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        /* 2. Responsive Grid: Tự động co giãn thẻ Card */
        .events-grid {
            display: grid;
            /* Tự động chia cột: Tối thiểu 280px, còn lại chia đều */
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 24px;
        }

        /* 3. Giao diện Mobile (Màn hình < 992px) */
        @media (max-width: 991px) {
            .mobile-filter-trigger {
                display: flex; /* Hiện nút lọc */
            }

            /* Sidebar trượt từ trái sang */
            .filters-sidebar {
                position: fixed;
                top: 0;
                left: -100%; /* Giấu sang bên trái */
                width: 280px;
                height: 100vh;
                background: #fff;
                z-index: 1000;
                padding: 20px;
                overflow-y: auto;
                transition: left 0.3s ease;
                box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            }

            .filters-sidebar.active {
                left: 0; /* Trượt ra */
            }

            /* Nút đóng sidebar */
            .sidebar-close-btn {
                display: block !important;
                position: absolute;
                top: 15px;
                right: 15px;
                background: none;
                border: none;
                font-size: 20px;
                color: #666;
                cursor: pointer;
            }

            /* Lớp phủ đen mờ (Overlay) */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.active {
                display: block;
            }
        }
        
        /* Ẩn nút đóng trên Desktop */
        .sidebar-close-btn {
            display: none;
        }
    </style> -->
@endpush


@section('content')
<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Khám phá sự kiện</h1>
        <p>Tìm kiếm và đặt vé cho các sự kiện tuyệt vời</p>
    </div>
</section>

<!-- Events Listing Section -->
<section class="events-listing">
    <div class="container">
        
        <!-- 👇 NÚT LỌC NỔI (FLOATING BUTTON) -->
        <!-- Chỉ để icon, bỏ chữ để nút tròn đẹp hơn -->
        <button class="mobile-filter-trigger" onclick="toggleSidebar()">
            <i class="fas fa-filter"></i>
        </button>

        <!-- Lớp phủ đen mờ -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <div class="listing-wrapper">
            <!-- ... (Phần còn lại giữ nguyên như cũ) ... -->
            <aside class="filters-sidebar" id="filtersSidebar">
                <button class="sidebar-close-btn" onclick="toggleSidebar()">
                    <i class="fas fa-times"></i>
                </button>
                <!-- ... Form lọc ... -->
                <div class="filter-header">
                    <h3>Bộ lọc</h3>
                    @if(request()->anyFilled(['category', 'city', 'date', 'max_price', 'search']))
                        <a href="{{ route('events.index') }}" class="btn-reset">Đặt lại</a>
                    @endif
                </div>
                
                <form action="{{ route('events.index') }}" method="GET" id="filterForm">
                     <!-- ... Nội dung form giữ nguyên ... -->
                    <input type="hidden" id="hiddenSort" name="sort" value="{{request('sort','upcoming')}}">

                     @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <!-- Category Filter -->
                    <div class="filter-group">
                        <h4>Danh mục</h4>
                        <div class="filter-options">
                            <label class="filter-option">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>📂 Tất cả</span>
                            </label>

                            @foreach($categories as $cat)
                            <label class="filter-option">
                                <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>{{ $cat->name }}</span>
                                <span class="count">({{ $cat->events_count }})</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Location Filter -->
                    <div class="filter-group">
                        <h4>Địa điểm</h4>
                        <div class="filter-options">
                            @php $cities = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Online']; @endphp
                            @foreach($cities as $city)
                            <label class="filter-option">
                                <input type="radio" name="city" value="{{ $city }}" {{ request('city') == $city ? 'checked' : '' }} onchange="this.form.submit()">
                                <span>{{ $city }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Date Filter -->
                    <div class="filter-group">
                        <h4>Thời gian</h4>
                        

                        <!-- 👇 PHẦN MỚI: CHỌN NGÀY TÙY Ý -->
                        <div class="custom-date-picker" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eee;">
                            
                            <div style="margin-bottom: 8px;">
                                <label style="font-size: 12px; display: block; color: #888;">Từ ngày:</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="font-size: 13px; padding: 6px;">
                            </div>
                            
                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; display: block; color: #888;">Đến ngày:</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="font-size: 13px; padding: 6px;">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">Áp dụng ngày</button>
                        </div>
                    </div>
                    
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h4>Khoảng giá tối đa</h4>
                        <div class="price-range">
                            <input type="range" 
                                   name="max_price" 
                                   min="0" 
                                   max="5000000" 
                                   step="100000" 
                                   value="{{ request('max_price', 5000000) }}" 
                                   id="priceRange"
                                   oninput="updatePriceLabel(this.value)"
                                   onchange="this.form.submit()">
                            
                            <div class="price-values">
                                <span>0 đ</span>
                                <span id="priceMaxDisplay">{{ number_format(request('max_price', 5000000)) }} đ</span>
                            </div>
                        </div>
                    </div>
                </form>
            </aside>

            <!-- Main Content -->
            <div class="events-content">
                <!-- ... (Phần nội dung giữ nguyên) ... -->
                 <div class="content-header">
                    <div class="results-info">
                        <h3>Tìm thấy <span id="resultCount">{{ $events->total() }}</span> sự kiện</h3>
                    </div>
                    <div class="view-options">
                    <select class="form-control" 
                                onchange="document.getElementById('hiddenSort').value = this.value; document.getElementById('filterForm').submit();">                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="upcoming" {{ request('sort') == 'upcoming' ? 'selected' : '' }}>Sắp diễn ra</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                        </select>

                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" onclick="setView('grid')"><i class="fas fa-th"></i></button>
                            <button class="view-btn" data-view="list" onclick="setView('list')"><i class="fas fa-list"></i></button>
                        </div>
                    </div>
                </div>

                @if($events->count() > 0)
                    <div class="events-grid" id="eventsGrid">
                        @foreach($events as $event)
                        <div class="event-card">
                            <div class="event-image">
                                <a href="{{ route('events.show', $event->slug) }}">
                                    <img src="{{ $event->featured_image }}" alt="{{ $event->title }}">
                                </a>
                                @if($event->is_featured)
                                    <span class="event-badge hot">🔥 Hot</span>
                                @else
                                    <div class="event-badge">{{ $event->category->name }}</div>
                                @endif
                            </div>
                            <div class="event-content">
                                <div class="event-category">{{ $event->category->name }}</div>
                                <h3 class="event-title">
                                    <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                                </h3>
                                <div class="event-info">
                                    <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->start_datetime)->format('d/m/Y') }}</span>
                                    <span><i class="fas fa-map-marker-alt"></i> {{ $event->venue_city }}</span>
                                </div>
                                <div class="event-footer">
                                    <div class="event-price">
                                        <span class="price-label">Từ</span>
                                        <span class="price-value">
                                            {{ $event->min_price == 0 ? 'Miễn phí' : number_format($event->min_price) . ' VNĐ' }}
                                        </span>
                                    </div>
                                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-small btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="pagination-wrapper">
                        {{ $events->withQueryString()->links() }}
                    </div>
                @else
                    <div class="no-results" style="text-align: center; padding: 50px;">
                        <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
                        <h3>Không tìm thấy sự kiện nào</h3>
                        <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function updatePriceLabel(value) {
        const formatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        document.getElementById('priceMaxDisplay').innerText = formatted;
    }

    function setView(viewType) {
        const grid = document.getElementById('eventsGrid');
        const btns = document.querySelectorAll('.view-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        const activeBtn = document.querySelector(`.view-btn[data-view="${viewType}"]`);
        if(activeBtn) activeBtn.classList.add('active');

        if (viewType === 'list') {
            grid.classList.add('list-view');
            grid.style.gridTemplateColumns = '1fr';
        } else {
            grid.classList.remove('list-view');
            grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
        }
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('filtersSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        
        if (sidebar.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }
</script>
@endpush
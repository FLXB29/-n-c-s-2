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
                        <div class="location-search-container">
                            <input type="text" 
                                   id="locationInput" 
                                   name="city" 
                                   class="form-control" 
                                   list="vietnam-provinces" 
                                   placeholder="Nhập hoặc chọn tỉnh/thành..."
                                   value="{{ request('city') }}"
                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                            <datalist id="vietnam-provinces">
                                <option value="Hà Nội">
                                <option value="Hồ Chí Minh">
                                <option value="Đà Nẵng">
                                <option value="Hải Phòng">
                                <option value="Cần Thơ">
                                <option value="An Giang">
                                <option value="Bà Rịa - Vũng Tàu">
                                <option value="Bắc Giang">
                                <option value="Bắc Kạn">
                                <option value="Bạc Liêu">
                                <option value="Bắc Ninh">
                                <option value="Bến Tre">
                                <option value="Bình Định">
                                <option value="Bình Dương">
                                <option value="Bình Phước">
                                <option value="Bình Thuận">
                                <option value="Cà Mau">
                                <option value="Cao Bằng">
                                <option value="Đắk Lắk">
                                <option value="Đắk Nông">
                                <option value="Điện Biên">
                                <option value="Đồng Nai">
                                <option value="Đồng Tháp">
                                <option value="Gia Lai">
                                <option value="Hà Giang">
                                <option value="Hà Nam">
                                <option value="Hà Tĩnh">
                                <option value="Hải Dương">
                                <option value="Hậu Giang">
                                <option value="Hòa Bình">
                                <option value="Hưng Yên">
                                <option value="Khánh Hòa">
                                <option value="Kiên Giang">
                                <option value="Kon Tum">
                                <option value="Lai Châu">
                                <option value="Lâm Đồng">
                                <option value="Lạng Sơn">
                                <option value="Lào Cai">
                                <option value="Long An">
                                <option value="Nam Định">
                                <option value="Nghệ An">
                                <option value="Ninh Bình">
                                <option value="Ninh Thuận">
                                <option value="Phú Thọ">
                                <option value="Phú Yên">
                                <option value="Quảng Bình">
                                <option value="Quảng Nam">
                                <option value="Quảng Ngãi">
                                <option value="Quảng Ninh">
                                <option value="Quảng Trị">
                                <option value="Sóc Trăng">
                                <option value="Sơn La">
                                <option value="Tây Ninh">
                                <option value="Thái Bình">
                                <option value="Thái Nguyên">
                                <option value="Thanh Hóa">
                                <option value="Thừa Thiên Huế">
                                <option value="Tiền Giang">
                                <option value="Trà Vinh">
                                <option value="Tuyên Quang">
                                <option value="Vĩnh Long">
                                <option value="Vĩnh Phúc">
                                <option value="Yên Bái">
                                <option value="Online">
                            </datalist>
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
                        <h4>Khoảng giá (VNĐ)</h4>
                        <div class="price-range-inputs" style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                            <div class="input-wrapper" style="flex: 1;">
                                <input type="number" 
                                       name="min_price" 
                                       id="minPriceInput"
                                       class="form-control" 
                                       placeholder="Từ" 
                                       min="0"
                                       value="{{ request('min_price') }}"
                                       style="font-size: 13px; padding: 6px;">
                            </div>
                            <span style="color: #888;">-</span>
                            <div class="input-wrapper" style="flex: 1;">
                                <input type="number" 
                                       name="max_price" 
                                       id="maxPriceInput"
                                       class="form-control" 
                                       placeholder="Đến" 
                                       min="0"
                                       value="{{ request('max_price') }}"
                                       style="font-size: 13px; padding: 6px;">
                            </div>
                        </div>
                        
                        <!-- Nút áp dụng riêng cho giá để tránh reload liên tục khi gõ -->
                        <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="filterEvents()">
                            Áp dụng giá
                        </button>
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
                    <select class="form-control" id="sortSelect"
                                onchange="document.getElementById('hiddenSort').value = this.value; filterEvents();">                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
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

                <div id="events-container">
                    @include('events._list')
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // AJAX Filtering Logic
    function filterEvents(url = null) {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        
        // Add sort parameter
        const sortVal = document.getElementById('hiddenSort').value;
        if(sortVal) params.set('sort', sortVal);

        // If a URL is provided (pagination), use it but keep current filters
        // Actually, pagination links already contain query params, but we might want to ensure current form state is respected if user changed filters but clicked page 2 of OLD filters.
        // Better approach: When filtering, always go to page 1 (default). When clicking pagination, use that URL.
        
        let fetchUrl = "{{ route('events.index') }}?" + params.toString();
        
        if (url) {
            fetchUrl = url;
        }

        // Update Browser URL
        window.history.pushState({}, '', fetchUrl);

        // Show loading state (optional)
        document.getElementById('events-container').style.opacity = '0.5';

        fetch(fetchUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('events-container').innerHTML = data.html;
            document.getElementById('resultCount').innerText = data.total;
            document.getElementById('events-container').style.opacity = '1';
            
            // Re-apply view mode (grid/list)
            const activeViewBtn = document.querySelector('.view-btn.active');
            if(activeViewBtn) {
                setView(activeViewBtn.dataset.view);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Attach Event Listeners to Inputs
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filterForm');
        
        // Radio buttons and Checkboxes
        const inputs = form.querySelectorAll('input[type="radio"], input[type="checkbox"]');
        inputs.forEach(input => {
            input.addEventListener('change', () => filterEvents());
            // Remove old onchange
            input.removeAttribute('onchange'); 
        });

        // Location Input
        const locationInput = document.getElementById('locationInput');
        if(locationInput) {
            locationInput.addEventListener('change', () => filterEvents());
        }

        // Range Input
        // const range = document.getElementById('priceRange');
        // if(range) {
        //     range.addEventListener('change', () => filterEvents());
        //     range.removeAttribute('onchange');
        // }

        const priceInputs = form.querySelectorAll('input[type="number"]');
        priceInputs.forEach(input => {
            input.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    filterEvents();
                }
            });
        });


        // Pagination Clicks
        document.getElementById('events-container').addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
                e.preventDefault();
                const url = e.target.href;
                filterEvents(url);
            }
        });
        
        // Prevent default form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            filterEvents();
        });
    });

    function updatePriceLabel(value) {
        const formatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        document.getElementById('priceMaxDisplay').innerText = formatted;
    }

    function setView(viewType) {
        const grid = document.getElementById('eventsGrid');
        if(!grid) return; // Might be no results

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
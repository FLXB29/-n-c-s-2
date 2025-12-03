@extends('layouts.app')

@section('title', $event->title . ' - EventHub')

@push('styles')
    <!-- Đảm bảo bạn đã copy file event-detail.css vào public/css/ -->
    <link rel="stylesheet" href="{{ asset('css/event-detail.css') }}">
@endpush

@section('content')
    <!-- Event Detail Section -->
    <section class="event-detail">
        <div class="container">
            <div class="detail-wrapper">
                <!-- Left Column -->
                <div class="detail-main">
                    <!-- Event Cover Image -->
                    <div class="event-cover">
                        <img src="{{ Str::startsWith($event->featured_image, 'http') ? $event->featured_image : asset($event->featured_image) }}" alt="{{ $event->title }}">
                        <div class="cover-overlay">
                            @if($event->is_featured)
                                <span class="event-badge hot">🔥 Hot Event</span>
                            @else
                                <span class="event-badge">{{ $event->category->name }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Event Title & Info -->
                    <div class="event-header">
                        <div class="event-category-tag">{{ $event->category->name }}</div>
                        <h1 class="event-title">{{ $event->title }}</h1>
                        
                        <div class="event-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <strong>Thời gian</strong>
                                    <p>{{ \Carbon\Carbon::parse($event->start_datetime)->format('l, d/m/Y - H:i') }}</p>
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Địa điểm</strong>
                                    <p>{{ $event->venue_name }}</p>
                                    <small>{{ $event->venue_city }}</small>
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <strong>Sức chứa</strong>
                                    <p>800 chỗ</p> <!-- Có thể thay bằng $event->capacity nếu có -->
                                </div>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-ticket-alt"></i>
                                <div>
                                    <strong>Còn lại</strong>
                                    @php
                                        $totalRemaining = $event->ticketTypes->sum(function($ticket){
                                            return $ticket->remaining;
                                        });
                                    @endphp
                                    @if ($event->ticketTypes->count() > 0)
                                        @if($totalRemaining > 0)
                                            <p class="seats-left" style="color: #27ca6bff !important; font-weight: bold; ">{{$totalRemaining}} vé</p>
                                        @else
                                            <p class="seats-left" style="color: #e74c3c; font-weight: bold;">hết vé</p>
                                        @endif
                                    @else
                                    <p class="seats-left">Đang mở bán</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <div class="event-section">
                        <h2 class="section-title">Giới thiệu sự kiện</h2>
                        <div class="event-description">
                            {!! $event->description !!}
                        </div>
                    </div>

                    <!-- Location Map -->
                    <div class="event-section">
                        <h2 class="section-title">Địa điểm tổ chức</h2>
                        <div class="event-map">
                            <!-- Google Map Embed (Tạm thời hardcode, sau này có thể dùng toạ độ từ DB) -->
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0967433487647!2d105.85311931533405!3d21.02373869316477!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab8629e5a635%3A0x7cbf2c42d4e8a428!2sOpera%20House!5e0!3m2!1sen!2s!4v1634567890123!5m2!1sen!2s" 
                                    width="100%" 
                                    height="400" 
                                    style="border:0; border-radius: 0.75rem;" 
                                    allowfullscreen="" 
                                    loading="lazy"></iframe>
                            <div class="map-info">
                                <h4>{{ $event->venue_name }}</h4>
                                <p>📍 {{ $event->venue_address }}, {{ $event->venue_city }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Organizer Info -->
                    <div class="event-section">
                        <h2 class="section-title">Thông tin nhà tổ chức</h2>
                        <div class="organizer-card">
                            <div class="organizer-avatar">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($event->organizer->name ?? 'Organizer') }}&size=80&background=667eea&color=fff" alt="Organizer">
                            </div>
                            <div class="organizer-info">
                                <h4>{{ $event->organizer->name ?? 'Ban Tổ Chức' }}</h4>
                                <p>⭐ 4.8/5 (Đánh giá)</p>
                                <p>📧 Email: {{ $event->organizer->email ?? 'contact@eventhub.vn' }}</p>
                            </div>
                            <a href="#" class="btn btn-outline">Xem trang</a>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Card -->
                <aside class="booking-sidebar">
                    <div class="booking-card">
                        <div class="price-section">
                            <span class="price-label">Giá vé từ</span>
                            <span class="price-value">{{ number_format($event->min_price) }} VNĐ</span>
                        </div>

                        <form action="{{ route('events.checkout', $event->id) }}" method="POST" id="bookingForm">
                            @csrf
                            <input type="hidden" name="selected_seats" id="selectedSeatsInput">
                            <div class="ticket-types">
                                <h4>Chọn loại vé</h4>
                                @if($event->ticketTypes->count() >0)
                                @foreach($event->ticketTypes as $index => $ticket)
                                <label class="ticket-option">
                                    <input type="radio" name="ticket_type_id" value="{{ $ticket->id }}" 
                                           data-price="{{ $ticket->price }}" 
                                           {{ $index == 0 ? 'checked' : '' }}
                                           onchange="updateTotal()">
                                    <div class="ticket-details">
                                        <div class="ticket-name">
                                            <strong>{{ $ticket->name }}</strong>
                                            <span class="ticket-stock">Còn {{ $ticket->remaining }} vé</span>
                                        </div>
                                        <div class="ticket-price">{{ number_format($ticket->price) }} VNĐ</div>
                                    </div>
                                </label>
                                @endforeach
                                @endif
                            </div>

                            <div class="quantity-section">
                                <h4>Số lượng</h4>
                                <div class="quantity-control">
                                    <button type="button" class="qty-btn" onclick="decreaseQuantity()">-</button>
                                    <input type="number" name="quantity" id="ticketQuantity" value="1" min="1" max="10" readonly>
                                    <button type="button" class="qty-btn" onclick="increaseQuantity()">+</button>
                                </div>
                            </div>

                            <div class="total-section">
                                <div class="total-row">
                                    <span>Tổng cộng:</span>
                                    <strong id="totalPrice">{{ number_format($event->min_price) }} VNĐ</strong>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary btn-full btn-large" onclick="openModal('seatSelectionModal')">
                                <i class="fas fa-chair"></i> Chọn chỗ ngồi
                            </button>
                            
                            <button type="submit" class="btn btn-outline btn-full" style="margin-top: 10px;">
                                <i class="fas fa-shopping-cart"></i> Mua ngay
                            </button>
                        </form>

                        <div class="booking-features">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>Thanh toán an toàn</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Vé điện tử</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- Seat Selection Modal (Giữ nguyên HTML tĩnh) -->
    <div id="seatSelectionModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 class="modal-title">Chọn chỗ ngồi</h3>
                <button class="modal-close" onclick="closeModal('seatSelectionModal')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="seat-selection-wrapper">
                    <!-- Chú thích màu -->
                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="seat-demo" style="border: 1px solid #6c5ce7; background: #fff;"></div> Còn trống
                        </div>
                        <div class="legend-item">
                            <div class="seat-demo" style="background: #6c5ce7; border: none;"></div> Đang chọn
                        </div>
                        <div class="legend-item">
                            <div class="seat-demo" style="background: #b2bec3; border: none;"></div> Đã bán
                        </div>
                    </div>

                    <div class="stage">SÂN KHẤU</div>
                    
                    <!-- Container chứa ghế -->
                    <div class="seats-container" id="seatsContainer">
                        <!-- JS sẽ vẽ ghế vào đây -->
                    </div>

                    <!-- Thanh thông tin phía dưới -->
                    <div style="margin-top: 30px; width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <div>
                            <strong>Ghế đã chọn: </strong>
                            <span id="selectedSeatsDisplay" style="color: #6c5ce7; font-weight: bold;">--</span>
                        </div>
                        <div>
                            <strong>Tổng tiền: </strong>
                            <span id="modalTotalPrice" style="color: #6c5ce7; font-size: 18px; font-weight: bold;">0 VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('seatSelectionModal')">Hủy</button>
                <button class="btn btn-primary" onclick="confirmSeats()">Xác nhận</button>
            </div>
        </div>
    </div>
@endsection


{{-- 👇 CHỈ GIỮ LẠI DUY NHẤT KHỐI SCRIPT NÀY --}}
@push('scripts')
<script>
    // 1. CẤU HÌNH CHO FILE JS BÊN NGOÀI
    window.eventConfig = {
        eventId: {{ $event->id }},
        apiSeatsUrl: "{{ route('events.seats', $event->id) }}",
        ticketTypes: @json($event->ticketTypes)
    };

    // 2. HÀM CẬP NHẬT GIÁ TIỀN Ở FORM CHÍNH (BÊN NGOÀI MODAL)
    function updateTotal() {
        const quantityInput = document.getElementById('ticketQuantity');
        const quantity = parseInt(quantityInput.value) || 0;
        
        // Tìm radio button đang được chọn
        const selectedTicket = document.querySelector('input[name="ticket_type_id"]:checked');
        
        if (selectedTicket) {
            const price = parseInt(selectedTicket.dataset.price);
            const total = price * quantity;
            
            // Format tiền Việt Nam
            const formattedTotal = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
            document.getElementById('totalPrice').innerText = formattedTotal;
        }
    }

    // Các hàm tăng giảm số lượng
    function increaseQuantity() {
        const input = document.getElementById('ticketQuantity');
        if (parseInt(input.value) < 10) {
            input.value = parseInt(input.value) + 1;
            updateTotal();
        }
    }

    function decreaseQuantity() {
        const input = document.getElementById('ticketQuantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateTotal();
        }
    }

    // Hàm mở/đóng Modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Chạy lần đầu khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        updateTotal();
    });
</script>

<!-- Nhúng file JS xử lý ghế -->
<script src="{{ asset('js/seat-selection.js') }}"></script>
@endpush
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
                {{-- <div>
                    <span>Còn lại {{$event->total_tickets - $event->tickets_sold}}</span>
                </div> --}}
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

    <div class="pagination-wrapper mt-4 d-flex justify-content-center">
        {{ $events->withQueryString()->links() }}
    </div>
@else
    <div class="no-results" style="text-align: center; padding: 50px;">
        <i class="fas fa-search" style="font-size: 48px; color: #ddd; margin-bottom: 20px;"></i>
        <h3>Không tìm thấy sự kiện nào</h3>
        <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
    </div>
@endif
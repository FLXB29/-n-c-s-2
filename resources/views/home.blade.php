@extends('layouts.app')

@section('title', 'Trang chủ - EventHub')

@section('content')
<!-- Hero Banner -->
<section class="hero-banner">
    <div class="banner-slider">
        @foreach($featuredEvents->take(3) as $index => $event)
        <div class="banner-slide {{ $index === 0 ? 'active' : '' }}">
            <div class="banner-image" style="background-image: url('{{ $event->featured_image }}')"></div>
            <div class="banner-content">
                <div class="container">
                    <span class="banner-category">{{ $event->category->name }}</span>
                    <h1>{{ $event->title }}</h1>
                    <p>{{ $event->short_description }}</p>
                    <div class="banner-meta">
                        <span><i class="fas fa-calendar"></i> {{ $event->start_datetime->format('d/m/Y H:i') }}</span>
                        <span><i class="fas fa-map-marker-alt"></i> {{ $event->venue_city }}</span>
                    </div>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-lg">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="banner-controls">
        <button class="banner-prev"><i class="fas fa-chevron-left"></i></button>
        <div class="banner-dots">
            @foreach($featuredEvents->take(3) as $index => $event)
            <span class="banner-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @endforeach
        </div>
        <button class="banner-next"><i class="fas fa-chevron-right"></i></button>
    </div>
</section>

<!-- Featured Events -->
<section class="featured-events">
    <div class="container">
        <div class="section-header">
            <h2>Sự kiện nổi bật</h2>
            <p>Khám phá những sự kiện hot nhất đang chờ đón bạn</p>
        </div>
        
        <div class="events-grid">
            @foreach($featuredEvents as $event)
            <div class="event-card">
                <div class="event-image">
                    <img src="{{ $event->featured_image }}" alt="{{ $event->title }}">
                    <div class="event-badge">{{ $event->category->name }}</div>
                    @if($event->is_featured)
                    <div class="featured-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    @endif
                </div>
                <div class="event-content">
                    <h3>{{ $event->title }}</h3>
                    <div class="event-meta">
                        <div class="event-date">
                            <i class="fas fa-calendar"></i>
                            {{ $event->start_datetime->format('d/m/Y') }}
                        </div>
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $event->venue_city }}
                        </div>
                    </div>
                    <div class="event-price">
                        {{ $event->formatted_price }}
                    </div>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm">
                        Mua vé ngay
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Khám phá theo danh mục</h2>
            <p>Tìm kiếm sự kiện theo sở thích của bạn</p>
        </div>
        
        <div class="categories-grid">
            @foreach($categories as $category)
            <a href="{{ route('events.index', ['category' => $category->slug]) }}" class="category-card">
                <div class="category-icon" style="background: {{ $category->color }}">
                    <i class="{{ $category->icon }}"></i>
                </div>
                <h4>{{ $category->name }}</h4>
                <span>{{ $category->events_count ?? 0 }} sự kiện</span>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endsection
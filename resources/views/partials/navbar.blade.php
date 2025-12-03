<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <!-- Logo -->
            <div class="nav-brand">
                <a href="{{ route('home') }}" class="brand-link">
                    <div class="brand-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="brand-text">EventHub</span>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="nav-search">
                <form action="{{ route('events.index') }}" method="GET" class="search-form">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Tìm kiếm sự kiện..." 
                               value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Navigation Links -->
            <div class="nav-menu" id="navMenu">
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}" class="nav-link">
                        <i class="fas fa-home"></i> Trang chủ
                    </a></li>
                    <li><a href="{{ route('events.index') }}" class="nav-link">
                        <i class="fas fa-calendar"></i> Sự kiện
                    </a></li>
                    <li class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <i class="fas fa-th-large"></i> Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('events.index', ['category' => 'music']) }}">
                                <i class="fas fa-music"></i> Âm nhạc
                            </a></li>
                            <li><a href="{{ route('events.index', ['category' => 'sports']) }}">
                                <i class="fas fa-futbol"></i> Thể thao
                            </a></li>
                            <li><a href="{{ route('events.index', ['category' => 'workshop']) }}">
                                <i class="fas fa-tools"></i> Workshop
                            </a></li>
                            <li><a href="{{ route('events.index', ['category' => 'conference']) }}">
                                <i class="fas fa-briefcase"></i> Hội thảo
                            </a></li>
                        </ul>
                    </li>
                </ul>
                
                <!-- Mobile Auth Links (shown only in mobile menu) -->
                <div class="mobile-auth-links">
                    @guest
                        <a href="{{ route('login') }}" class="mobile-auth-btn">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="mobile-auth-btn primary">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </a>
                    @else
                        <div class="mobile-user-info">
                            <img src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('images/default-avatar.png') }}" 
                                 alt="Avatar" class="mobile-user-avatar">
                            <span>{{ auth()->user()->full_name }}</span>
                        </div>
                        <a href="{{ route('user.dashboard') }}" class="mobile-auth-btn">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        @if(auth()->user()->isOrganizer())
                        <a href="{{ route('organizer.dashboard') }}" class="mobile-auth-btn">
                            <i class="fas fa-calendar-plus"></i> Quản lý sự kiện
                        </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="mobile-auth-btn">
                            <i class="fas fa-cogs"></i> Admin Panel
                        </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="mobile-auth-btn logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </button>
                        </form>
                    @endguest
                </div>
            </div>

            <!-- Auth Buttons -->
            <div class="nav-auth">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Đăng ký
                    </a>
                @else
                    <div class="user-dropdown">
                        <button class="user-toggle" id="userToggle">
                            <img src="{{ auth()->user()->avatar ? asset(auth()->user()->avatar) : asset('images/default-avatar.png') }}" 
                                 alt="Avatar" class="user-avatar">
                            <span class="user-name">{{ auth()->user()->full_name }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <ul class="user-menu" id="userMenu">
                            <li><a href="{{ route('user.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a></li>
                            @if(auth()->user()->isOrganizer())
                            <li><a href="{{ route('organizer.dashboard') }}">
                                <i class="fas fa-calendar-plus"></i> Quản lý sự kiện
                            </a></li>
                            @endif
                            @if(auth()->user()->isAdmin())
                            <li><a href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-cogs"></i> Admin Panel
                            </a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="logout-form">
                                    @csrf
                                    <button type="submit" class="logout-btn">
                                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endguest
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="mobile-menu-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
</nav>
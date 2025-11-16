<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Nền tảng Quản lý & Bán vé Sự kiện</title>
    <link rel="stylesheet" href={{ asset("css/main.css") }}>
    <link rel="stylesheet" href={{ asset("css/components.css") }}>
    <link rel="stylesheet" href={{ asset("css/homepage.css") }}>
    <link rel="stylesheet" href={{ asset("css/responsive.css") }}>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <div class="nav-brand">
                    <i class="fas fa-ticket-alt"></i>
                    <span>EventHub</span>
                </div>
                
                <div class="nav-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm sự kiện...">
                </div>
                
                <div class="nav-menu">
                    <a href="index.html" class="nav-link active">Trang chủ</a>
                    <a href="events.html" class="nav-link">Sự kiện</a>
                    <div class="nav-dropdown">
                        <a href="#" class="nav-link">Danh mục <i class="fas fa-chevron-down"></i></a>
                        <div class="dropdown-menu">
                            <a href="events.html?category=music">🎵 Âm nhạc</a>
                            <a href="events.html?category=sports">⚽ Thể thao</a>
                            <a href="events.html?category=workshop">🛠️ Workshop</a>
                            <a href="events.html?category=conference">💼 Hội thảo</a>
                            <a href="events.html?category=festival">🎉 Lễ hội</a>
                        </div>
                    </div>
                    <a href="login.html" class="btn btn-outline">Đăng nhập</a>
                    <a href="register.html" class="btn btn-primary">Đăng ký</a>
                </div>
                
                <div class="nav-mobile-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner Section -->
    <section class="hero-banner">
        <div class="banner-slider">
            <!-- Slide 1 -->
            <div class="banner-slide active">
                <div class="banner-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="banner-overlay"></div>
                </div>
                <div class="container">
                    <div class="banner-content">
                        <span class="banner-tag">🔥 Đang hot</span>
                        <h1>Đại Nhạc Hội Mùa Hè 2025</h1>
                        <p>Cùng hòa mình vào không khí sôi động với các nghệ sĩ hàng đầu Việt Nam</p>
                        <div class="banner-info">
                            <span><i class="fas fa-calendar"></i> 15/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Sân vận động Mỹ Đình, Hà Nội</span>
                            <span class="price">Từ 500.000 VNĐ</span>
                        </div>
                        <div class="banner-actions">
                            <a href="event-detail.html?id=1" class="btn btn-large btn-primary">Mua vé ngay</a>
                            <a href="event-detail.html?id=1" class="btn btn-large btn-outline-white">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 -->
            <div class="banner-slide">
                <div class="banner-image" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="banner-overlay"></div>
                </div>
                <div class="container">
                    <div class="banner-content">
                        <span class="banner-tag">🎭 Mới nhất</span>
                        <h1>Triển Lãm Nghệ Thuật Đương Đại</h1>
                        <p>Khám phá những tác phẩm nghệ thuật độc đáo từ các họa sĩ nổi tiếng</p>
                        <div class="banner-info">
                            <span><i class="fas fa-calendar"></i> 20/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Bảo tàng Mỹ thuật, TP.HCM</span>
                            <span class="price">Từ 200.000 VNĐ</span>
                        </div>
                        <div class="banner-actions">
                            <a href="event-detail.html?id=2" class="btn btn-large btn-primary">Mua vé ngay</a>
                            <a href="event-detail.html?id=2" class="btn btn-large btn-outline-white">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 -->
            <div class="banner-slide">
                <div class="banner-image" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="banner-overlay"></div>
                </div>
                <div class="container">
                    <div class="banner-content">
                        <span class="banner-tag">💼 Sắp diễn ra</span>
                        <h1>Hội Thảo Công Nghệ AI 2025</h1>
                        <p>Cơ hội học hỏi từ các chuyên gia hàng đầu về AI và Machine Learning</p>
                        <div class="banner-info">
                            <span><i class="fas fa-calendar"></i> 25/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Trung tâm Hội nghị Quốc gia</span>
                            <span class="price">Từ 1.500.000 VNĐ</span>
                        </div>
                        <div class="banner-actions">
                            <a href="event-detail.html?id=3" class="btn btn-large btn-primary">Mua vé ngay</a>
                            <a href="event-detail.html?id=3" class="btn btn-large btn-outline-white">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Slider Controls -->
        <div class="banner-controls">
            <button class="banner-prev"><i class="fas fa-chevron-left"></i></button>
            <div class="banner-dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            <button class="banner-next"><i class="fas fa-chevron-right"></i></button>
        </div>
    </section>

    <!-- Featured Events Section -->
    <section class="featured-events">
        <div class="container">
            <div class="section-header">
                <h2>Sự kiện nổi bật</h2>
                <a href="events.html" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="events-grid">
                <!-- Event Card 1 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=500" alt="Event">
                        <span class="event-badge hot">🔥 Hot</span>
                    </div>
                    <div class="event-content">
                        <div class="event-category">Âm nhạc</div>
                        <h3 class="event-title">Concert Acoustic - Những Bản Tình Ca</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 28/10/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Nhà hát Lớn Hà Nội</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">300.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=4" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 2 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=500" alt="Event">
                        <span class="event-badge new">✨ Mới</span>
                    </div>
                    <div class="event-content">
                        <div class="event-category">Thể thao</div>
                        <h3 class="event-title">Giải Chạy Marathon Quốc Tế</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 05/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Công viên Thống Nhất</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">250.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=5" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 3 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=500" alt="Event">
                    </div>
                    <div class="event-content">
                        <div class="event-category">Workshop</div>
                        <h3 class="event-title">Workshop Nhiếp Ảnh Chuyên Nghiệp</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 10/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Studio ABC, TP.HCM</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">800.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=6" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 4 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=500" alt="Event">
                    </div>
                    <div class="event-content">
                        <div class="event-category">Hội thảo</div>
                        <h3 class="event-title">Tech Summit - Digital Transformation</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 18/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Diamond Plaza, TP.HCM</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">1.200.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=7" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 5 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=500" alt="Event">
                        <span class="event-badge hot">🔥 Hot</span>
                    </div>
                    <div class="event-content">
                        <div class="event-category">Lễ hội</div>
                        <h3 class="event-title">Food Festival - Lễ Hội Ẩm Thực 2025</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 22/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> Công viên 23/9, TP.HCM</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">150.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=8" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <!-- Event Card 6 -->
                <div class="event-card">
                    <div class="event-image">
                        <img src="https://images.unsplash.com/photo-1506157786151-b8491531f063?w=500" alt="Event">
                    </div>
                    <div class="event-content">
                        <div class="event-category">Âm nhạc</div>
                        <h3 class="event-title">Đêm Nhạc Jazz & Blues</h3>
                        <div class="event-info">
                            <span><i class="fas fa-calendar"></i> 30/11/2025</span>
                            <span><i class="fas fa-map-marker-alt"></i> The Jazz Club, Hà Nội</span>
                        </div>
                        <div class="event-footer">
                            <div class="event-price">
                                <span class="price-label">Từ</span>
                                <span class="price-value">400.000 VNĐ</span>
                            </div>
                            <a href="event-detail.html?id=9" class="btn btn-small btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="section-header">
                <h2>Khám phá theo danh mục</h2>
                <p>Tìm kiếm sự kiện theo sở thích của bạn</p>
            </div>
            
            <div class="categories-grid">
                <a href="events.html?category=music" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3>Âm nhạc</h3>
                    <p>125 sự kiện</p>
                </a>

                <a href="events.html?category=sports" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <h3>Thể thao</h3>
                    <p>86 sự kiện</p>
                </a>

                <a href="events.html?category=workshop" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Workshop</h3>
                    <p>64 sự kiện</p>
                </a>

                <a href="events.html?category=conference" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Hội thảo</h3>
                    <p>92 sự kiện</p>
                </a>

                <a href="events.html?category=festival" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Lễ hội</h3>
                    <p>48 sự kiện</p>
                </a>

                <a href="events.html?category=art" class="category-card">
                    <div class="category-icon" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3>Nghệ thuật</h3>
                    <p>71 sự kiện</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-ticket-alt"></i>
                        <span>EventHub</span>
                    </div>
                    <p>Nền tảng quản lý và bán vé sự kiện hàng đầu Việt Nam</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-section">
                    <h4>Về chúng tôi</h4>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Tin tức</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Hỗ trợ</h4>
                    <ul>
                        <li><a href="#">Trung tâm trợ giúp</a></li>
                        <li><a href="#">Hướng dẫn mua vé</a></li>
                        <li><a href="#">Chính sách hoàn tiền</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Dành cho nhà tổ chức</h4>
                    <ul>
                        <li><a href="#">Tạo sự kiện</a></li>
                        <li><a href="#">Quản lý sự kiện</a></li>
                        <li><a href="#">Báo cáo & Phân tích</a></li>
                        <li><a href="#">Hướng dẫn sử dụng</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Liên hệ</h4>
                    <ul>
                        <li><i class="fas fa-phone"></i> 1900-xxxx</li>
                        <li><i class="fas fa-envelope"></i> support@eventhub.vn</li>
                        <li><i class="fas fa-map-marker-alt"></i> Hà Nội, Việt Nam</li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 EventHub. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#">Điều khoản sử dụng</a>
                    <a href="#">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    <script src={{ asset("js/main.js") }}></script>
</body>
</html>

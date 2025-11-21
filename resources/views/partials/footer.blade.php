<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <!-- Company Info -->
            <div class="footer-section">
                <div class="footer-brand">
                    <div class="brand-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="brand-text">EventHub</span>
                </div>
                <p class="footer-description">
                    Nền tảng quản lý và bán vé sự kiện hàng đầu Việt Nam. 
                    Kết nối người tổ chức với khán giả một cách dễ dàng và hiệu quả.
                </p>
                <div class="social-links">
                    <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-section">
                <h4 class="footer-title">Liên kết nhanh</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li><a href="{{ route('events.index') }}">Sự kiện</a></li>
                    <li><a href="#">Về chúng tôi</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="#">Trợ giúp</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="footer-section">
                <h4 class="footer-title">Danh mục</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('events.index', ['category' => 'music']) }}">Âm nhạc</a></li>
                    <li><a href="{{ route('events.index', ['category' => 'sports']) }}">Thể thao</a></li>
                    <li><a href="{{ route('events.index', ['category' => 'workshop']) }}">Workshop</a></li>
                    <li><a href="{{ route('events.index', ['category' => 'conference']) }}">Hội thảo</a></li>
                    <li><a href="{{ route('events.index', ['category' => 'festival']) }}">Lễ hội</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h4 class="footer-title">Thông tin liên hệ</h4>
                <ul class="contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt"></i>
                        <span>123 Đường ABC, Quận XYZ, TP.HCM</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>1900-xxxx</span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>support@eventhub.vn</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <p>&copy; {{ date('Y') }} EventHub. Tất cả quyền được bảo lưu.</p>
                <ul class="footer-bottom-links">
                    <li><a href="#">Điều khoản sử dụng</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
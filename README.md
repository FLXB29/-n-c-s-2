# EventHub - Hệ thống Quản lý Sự kiện & Bán vé

## 📋 Mô tả dự án
EventHub là một nền tảng quản lý và bán vé sự kiện trực tuyến, phục vụ cho cả người tổ chức sự kiện và người mua vé.

## 🎯 Tính năng chính

### Đã hoàn thành (Phase 1):
✅ **Trang chủ (index.html)**
- Banner slideshow với các sự kiện nổi bật
- Hiển thị danh sách sự kiện hot
- Phân loại theo danh mục
- Responsive đầy đủ

✅ **Trang danh sách sự kiện (events.html)**
- Bộ lọc đa tiêu chí (Danh mục, Địa điểm, Thời gian, Giá)
- Chế độ xem Grid/List
- Sắp xếp linh hoạt
- Phân trang
- Mobile-friendly với filter overlay

✅ **Trang chi tiết sự kiện (event-detail.html)**
- Thông tin chi tiết sự kiện
- Google Maps tích hợp
- Thông tin nhà tổ chức
- Hệ thống bình luận
- **Chức năng đặc sắc: Chọn chỗ ngồi với sơ đồ ghế tương tác**
- Sidebar đặt vé sticky
- Chia sẻ trên mạng xã hội

### Đang phát triển (Phase 2):
🔄 Đăng nhập / Đăng ký
🔄 User Dashboard (Quản lý vé đã mua)
🔄 Organizer Dashboard (Quản lý sự kiện)
🔄 Admin Panel

## 📁 Cấu trúc thư mục

```
DACS2/
├── index.html                  # Trang chủ
├── events.html                 # Danh sách sự kiện
├── event-detail.html          # Chi tiết sự kiện
├── login.html                 # (Sắp làm) Đăng nhập
├── register.html              # (Sắp làm) Đăng ký
├── user-dashboard.html        # (Sắp làm) Dashboard người dùng
├── organizer-dashboard.html   # (Sắp làm) Dashboard tổ chức
├── admin-dashboard.html       # (Sắp làm) Admin panel
│
├── css/
│   ├── main.css              # CSS chung (reset, navbar, footer, utilities)
│   ├── components.css        # Components (buttons, cards, forms, modals...)
│   ├── homepage.css          # CSS riêng cho trang chủ
│   ├── events.css            # CSS riêng cho trang danh sách
│   ├── event-detail.css      # CSS riêng cho trang chi tiết
│   └── responsive.css        # Media queries cho mọi trang
│
├── js/
│   ├── main.js               # JS chung (menu, slider, utilities)
│   ├── events.js             # JS cho trang events (filters, sorting)
│   └── seat-selection.js     # JS cho chức năng chọn ghế
│
├── images/                    # (Đang dùng placeholder từ Unsplash)
└── README.md                  # File này
```

## 🎨 Thiết kế & UI/UX

### Bảng màu:
- **Primary**: `#667eea` (Xanh tím)
- **Secondary**: `#764ba2` (Tím đậm)
- **Accent**: `#f5576c` (Đỏ hồng)
- **Text**: `#1a202c` (Đen xám)

### Typography:
- Font chính: Segoe UI, Tahoma, Geneva, Verdana, sans-serif
- Responsive font sizes

### Icons:
- Font Awesome 6.4.0

## 🚀 Hướng dẫn sử dụng

### 1. Chạy project:
```bash
# Mở file index.html bằng trình duyệt
# Hoặc dùng Live Server extension trong VS Code
```

### 2. Điều hướng:
- **Trang chủ**: `index.html`
- **Danh sách sự kiện**: Click "Sự kiện" hoặc "Xem tất cả"
- **Chi tiết sự kiện**: Click vào bất kỳ card sự kiện nào
- **Chọn ghế**: Trong trang chi tiết, click "Chọn chỗ ngồi"

## 💡 Các chức năng đặc biệt

### 1. Banner Slider (Trang chủ)
- Auto-play mỗi 5 giây
- Điều khiển bằng nút prev/next
- Hỗ trợ swipe trên mobile
- Keyboard navigation (arrow keys)

### 2. Filters (Trang events)
- Lọc theo nhiều tiêu chí
- Price range slider
- Custom date range
- Mobile overlay

### 3. Seat Selection (Trang chi tiết)
- Sơ đồ 10 hàng x 20 ghế
- Visual feedback (available/selected/sold)
- Click để chọn/bỏ chọn
- Giới hạn tối đa 10 ghế
- Cập nhật giá real-time

## 📱 Responsive Design

### Breakpoints:
- **Desktop**: > 992px
- **Tablet**: 768px - 992px
- **Mobile**: < 768px
- **Small Mobile**: < 480px

### Tính năng responsive:
✅ Mobile menu với hamburger icon
✅ Responsive grid layouts
✅ Touch-friendly buttons
✅ Optimized images
✅ Collapsible filters on mobile
✅ Sticky booking card

## 🔧 Công nghệ sử dụng

- **HTML5**: Semantic markup
- **CSS3**: Flexbox, Grid, Custom Properties, Animations
- **JavaScript (ES6+)**: Modules, Arrow functions, Promises
- **Font Awesome**: Icons
- **Google Maps**: Địa điểm (iframe embed)

## 📝 Hardcoded Data

Hiện tại tất cả dữ liệu đều được hardcode trong HTML. Khi tích hợp backend, cần:

### Events data structure (ví dụ):
```javascript
{
  id: 1,
  title: "Concert Acoustic",
  category: "music",
  date: "2025-10-28",
  time: "19:30",
  location: "Nhà hát Lớn Hà Nội",
  image: "...",
  price_from: 300000,
  tickets: [
    { type: "regular", price: 300000, available: 156 },
    { type: "vip", price: 500000, available: 78 },
    { type: "svip", price: 800000, available: 15 }
  ],
  seats: { ... },
  organizer: { ... }
}
```

## 🎯 Roadmap tiếp theo

### Phase 2: Authentication & User Management
- [ ] Trang đăng ký với validation
- [ ] Trang đăng nhập
- [ ] User dashboard với các tab:
  - [ ] Thông tin cá nhân
  - [ ] Vé của tôi (với QR code)
  - [ ] Lịch sử giao dịch
  
### Phase 3: Organizer Features
- [ ] Organizer dashboard
- [ ] Tạo/Sửa/Xóa sự kiện
- [ ] Upload ảnh sự kiện
- [ ] Quản lý loại vé
- [ ] Thiết lập sơ đồ ghế
- [ ] Công cụ check-in (scan QR)
- [ ] Báo cáo doanh thu

### Phase 4: Admin Panel
- [ ] Admin dashboard với thống kê
- [ ] Quản lý người dùng
- [ ] Quản lý sự kiện
- [ ] Quản lý danh mục
- [ ] Quản lý giao dịch

### Phase 5: Backend Integration
- [ ] Thiết kế database schema
- [ ] API endpoints
- [ ] Authentication & Authorization
- [ ] Payment gateway
- [ ] Email notifications
- [ ] QR code generation

## 🐛 Known Issues

- [ ] Seat selection: Cần thêm logic khóa ghế tạm thời (prevent double booking)
- [ ] Mobile: Booking card có thể che khuất content khi scroll
- [ ] Chưa có validation cho forms
- [ ] Chưa có error handling

## 📄 License

Dự án này là một đồ án học tập.

## 👥 Liên hệ

Nếu có câu hỏi hoặc góp ý, vui lòng tạo issue hoặc liên hệ trực tiếp.

---

**Cập nhật lần cuối**: 14/10/2025
**Version**: 1.0 (Phase 1 Complete)
"# -n-c-s-2" 

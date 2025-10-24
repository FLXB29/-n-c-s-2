# 🚀 Quick Start Guide - EventHub

## Mở project lần đầu

### Option 1: Dùng Live Server (Recommended)
1. Cài đặt extension **Live Server** trong VS Code
2. Right-click vào `navigation.html` hoặc `index.html`
3. Chọn "Open with Live Server"
4. Browser sẽ tự động mở trang

### Option 2: Mở trực tiếp
1. Double-click vào `navigation.html` để xem navigation guide
2. Hoặc `index.html` để xem trang chủ
3. Lưu ý: Một số tính năng cần HTTP server để chạy đúng

## 📍 Điểm bắt đầu

**File đầu tiên nên xem**: `navigation.html`
- Đây là trang điều hướng tổng quan
- Liệt kê tất cả các trang đã hoàn thành
- Link trực tiếp đến từng trang

## 🎯 Test các tính năng

### 1. Trang chủ (index.html)
✅ **Banner Slider**
- Tự động chuyển slide sau 5s
- Click nút prev/next để điều khiển
- Click dots để chọn slide cụ thể
- Swipe trên mobile
- Dùng arrow keys (← →)

✅ **Navigation**
- Hover vào "Danh mục" để xem dropdown
- Click hamburger menu trên mobile
- Search box (enter để search)

### 2. Danh sách sự kiện (events.html)
✅ **Filters**
- Tick checkbox để lọc theo category/location
- Select radio button cho date range
- Drag price slider
- Click "Áp dụng" hoặc "Đặt lại"

✅ **View Toggle**
- Click icon Grid (⊞) hoặc List (☰)
- Xem sự kiện ở 2 chế độ khác nhau

✅ **Sorting**
- Chọn trong dropdown "Sắp xếp theo"

✅ **Mobile Filters**
- Resize browser < 768px
- Sẽ xuất hiện nút filter floating bên phải dưới
- Click để mở filter sidebar

### 3. Chi tiết sự kiện (event-detail.html)
✅ **Chọn loại vé**
- Click vào một trong 3 loại vé
- Xem giá tự động cập nhật

✅ **Quantity Control**
- Click + / - để tăng giảm số lượng
- Hoặc nhập trực tiếp

✅ **Seat Selection** ⭐ (Tính năng đặc biệt)
- Click nút "Chọn chỗ ngồi"
- Modal xuất hiện với sơ đồ ghế
- Click ghế xanh để chọn (tối đa 10 ghế)
- Click lại để bỏ chọn
- Ghế xám = đã bán (không click được)
- Xem tổng tiền cập nhật real-time
- Click "Xác nhận" để hoàn tất

✅ **Comments**
- Scroll xuống phần bình luận
- Nhập comment và gửi
- Click Like/Reply

✅ **Share**
- Click các nút share social media
- Nút "Link" sẽ copy URL

## 🎨 Customize Colors

Mở file `css/main.css` và sửa `:root` variables:

```css
:root {
    --primary-color: #667eea;      /* Màu chính */
    --secondary-color: #764ba2;    /* Màu phụ */
    --accent-color: #f5576c;       /* Màu nhấn */
    /* ... */
}
```

## 📱 Test Responsive

### Trong Chrome DevTools:
1. F12 để mở DevTools
2. Ctrl + Shift + M (Toggle device toolbar)
3. Chọn device: iPhone, iPad, etc.
4. Hoặc kéo để resize manual

### Breakpoints:
- **> 992px**: Desktop
- **768px - 992px**: Tablet
- **< 768px**: Mobile
- **< 480px**: Small mobile

## 🐛 Debug

### JavaScript Errors:
- F12 → Console tab
- Xem errors màu đỏ

### CSS Issues:
- F12 → Elements tab
- Click vào element để xem styles
- Tick/untick properties để test

### Common Issues:

**1. Slider không chạy**
- Check console có error không
- Đảm bảo `js/main.js` được load

**2. Modal không mở**
- Check function `openModal()` trong main.js
- Đảm bảo modal ID đúng

**3. Filters không hoạt động**
- Check `js/events.js` được load chưa
- Xem console có error không

## 📝 Thêm sự kiện mới

Hardcoded trong HTML, copy paste đoạn này:

```html
<div class="event-card">
    <div class="event-image">
        <img src="URL_ANH" alt="Event">
        <span class="event-badge hot">🔥 Hot</span>
    </div>
    <div class="event-content">
        <div class="event-category">TÊN_DANH_MỤC</div>
        <h3 class="event-title">TÊN_SỰ_KIỆN</h3>
        <div class="event-info">
            <span><i class="fas fa-calendar"></i> NGÀY</span>
            <span><i class="fas fa-map-marker-alt"></i> ĐỊA_ĐIỂM</span>
        </div>
        <div class="event-footer">
            <div class="event-price">
                <span class="price-label">Từ</span>
                <span class="price-value">GIÁ VNĐ</span>
            </div>
            <a href="event-detail.html?id=ID" class="btn btn-small btn-primary">Xem chi tiết</a>
        </div>
    </div>
</div>
```

## 🔮 Next Steps (Khi làm backend)

1. **Replace hardcoded data với API calls**
```javascript
// Example:
fetch('/api/events')
    .then(res => res.json())
    .then(data => renderEvents(data));
```

2. **Form validation**
- Add validation cho login/register forms
- Use HTML5 validation attributes
- Add custom JS validation

3. **State management**
- Save selected filters to localStorage
- Remember user preferences
- Shopping cart management

4. **Authentication**
- JWT tokens
- Session management
- Protected routes

## 📚 Resources

- **Font Awesome Icons**: https://fontawesome.com/icons
- **CSS Tricks**: https://css-tricks.com/
- **MDN Web Docs**: https://developer.mozilla.org/
- **Unsplash Images**: https://unsplash.com/

## 🆘 Need Help?

- Check README.md cho overview
- Xem comments trong code
- Console.log() để debug
- Google error messages

---

**Happy Coding! 🎉**

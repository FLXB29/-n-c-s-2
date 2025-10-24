# 🚀 Phase 2 Quick Start Guide

## Hướng dẫn test các tính năng Phase 2

---

## 📋 Danh sách trang mới

1. **login.html** - Trang đăng nhập
2. **register.html** - Trang đăng ký  
3. **user-dashboard.html** - Dashboard người dùng

---

## 🧪 Test Scenarios

### 1. Test Login Page

**Đường dẫn:** `d:\DACS2\login.html`

#### Bước 1: Mở trang login
```
Mở file login.html trong trình duyệt
```

#### Bước 2: Test UI Elements
- ✅ Kiểm tra form hiển thị đúng
- ✅ Kiểm tra 2 nút social login (Google, Facebook)
- ✅ Kiểm tra divider "Hoặc"
- ✅ Kiểm tra auth aside bên phải (desktop)

#### Bước 3: Test Validation

**Test Empty Form:**
```
1. Bỏ trống email và password
2. Click "Đăng nhập"
3. Kết quả: Hiển thị lỗi "Vui lòng nhập email hợp lệ" và "Mật khẩu phải có ít nhất 6 ký tự"
```

**Test Invalid Email:**
```
1. Nhập email: "test" (không có @)
2. Click ra ngoài input
3. Kết quả: Hiển thị lỗi "Email không hợp lệ"
```

**Test Short Password:**
```
1. Nhập email: "test@example.com"
2. Nhập password: "12345" (< 6 ký tự)
3. Click "Đăng nhập"
4. Kết quả: Hiển thị lỗi "Mật khẩu phải có ít nhất 6 ký tự"
```

**Test Successful Login:**
```
1. Nhập email: "user@example.com"
2. Nhập password: "123456" (bất kỳ, >= 6 ký tự)
3. Click "Đăng nhập"
4. Kết quả: 
   - Hiển thị alert "Đang đăng nhập..."
   - Sau 1.5s: "Đăng nhập thành công!"
   - Redirect đến user-dashboard.html
```

#### Bước 4: Test Password Toggle
```
1. Nhập password
2. Click icon "eye" bên phải input
3. Kết quả: Mật khẩu hiển thị dạng text
4. Click lại icon (đã đổi thành "eye-slash")
5. Kết quả: Mật khẩu ẩn lại
```

#### Bước 5: Test Responsive
```
1. Resize trình duyệt xuống mobile (< 768px)
2. Kết quả: Auth aside ẩn đi, form full-width
3. Social buttons xếp dọc
```

---

### 2. Test Register Page

**Đường dẫn:** `d:\DACS2\register.html`

#### Bước 1: Mở trang register
```
Mở file register.html trong trình duyệt
```

#### Bước 2: Test Password Strength Indicator

**Test Weak Password:**
```
1. Nhập password: "abc" (chỉ chữ thường, < 8 ký tự)
2. Kết quả:
   - Progress bar màu đỏ, 33% width
   - Text: "Yếu" (màu đỏ)
```

**Test Medium Password:**
```
1. Nhập password: "Abc12345" (chữ hoa + thường + số, 8 ký tự)
2. Kết quả:
   - Progress bar màu vàng, 66% width
   - Text: "Trung bình" (màu vàng)
```

**Test Strong Password:**
```
1. Nhập password: "Abc123@#" (chữ hoa + thường + số + ký tự đặc biệt, >= 8 ký tự)
2. Kết quả:
   - Progress bar màu xanh, 100% width
   - Text: "Mạnh" (màu xanh)
```

#### Bước 3: Test Form Validation

**Test Phone Number:**
```
1. Nhập số điện thoại: "123" (< 10 số)
2. Click ra ngoài
3. Kết quả: Hiển thị lỗi "Số điện thoại không hợp lệ"

Valid format: 0123456789 (10 số, bắt đầu bằng 0)
```

**Test Password Confirmation:**
```
1. Nhập password: "Abc123@#"
2. Nhập confirm password: "Abc123" (khác)
3. Kết quả: Hiển thị lỗi "Mật khẩu không khớp"
```

**Test Terms Checkbox:**
```
1. Điền đầy đủ form nhưng KHÔNG check "Điều khoản sử dụng"
2. Click "Đăng ký"
3. Kết quả: Hiển thị lỗi "Bạn phải đồng ý với điều khoản sử dụng"
```

**Test Successful Registration:**
```
1. Nhập đầy đủ thông tin hợp lệ:
   - Họ tên: "Nguyễn Văn A"
   - SĐT: "0123456789"
   - Email: "test@example.com"
   - Password: "Abc123@#"
   - Confirm: "Abc123@#"
   - Check "Điều khoản"
2. Click "Đăng ký"
3. Kết quả:
   - Alert "Đang tạo tài khoản..."
   - Sau 2s: "Đăng ký thành công!"
   - Redirect đến login.html
```

#### Bước 4: Test Auth Aside (Right Side)
```
Desktop view (> 768px):
- Kiểm tra hiển thị stats grid (4 ô: 1,000+ / 50K+ / 100K+ / 4.8★)
- Kiểm tra testimonial card với avatar và text
```

---

### 3. Test User Dashboard

**Đường dẫn:** `d:\DACS2\user-dashboard.html`

#### Bước 1: Mở dashboard
```
Cách 1: Login thành công sẽ tự redirect
Cách 2: Mở trực tiếp user-dashboard.html
```

#### Bước 2: Test Top Navigation
```
1. Click icon "bell" (notification)
2. Quan sát badge dot màu đỏ

3. Click user avatar/name dropdown
4. Kết quả: Dropdown menu hiển thị với các options:
   - Hồ sơ
   - Cài đặt
   - Trợ giúp
   - ---
   - Đăng xuất
```

#### Bước 3: Test Sidebar Navigation

**Desktop View:**
```
Sidebar hiển thị cố định bên trái (280px width)
```

**Mobile View (< 992px):**
```
1. Sidebar ẩn ban đầu
2. Click hamburger icon (☰) ở top-left
3. Kết quả: Sidebar slide in từ bên trái
4. Click section nào đó
5. Kết quả: Sidebar tự đóng lại
```

**Test Section Navigation:**
```
1. Click "Tổng quan" → Hiển thị Overview section
2. Click "Vé của tôi" → Hiển thị Tickets section
3. Click "Lịch sử" → Hiển thị History section
4. Click "Yêu thích" → Hiển thị Favorites section
5. Click "Hồ sơ" → Hiển thị Profile section
6. Click "Cài đặt" → Hiển thị Settings section

Mỗi click sẽ:
- Active menu item (màu primary, border phải)
- Hiển thị section tương ứng
- Ẩn các section khác
- Fade in animation
```

#### Bước 4: Test Overview Section

**Stats Cards:**
```
Kiểm tra 4 stat cards:
1. 12 vé đã mua (icon xanh dương)
2. 3 sự kiện sắp tới (icon xanh lá)
3. 2.450.000đ tổng chi tiêu (icon tím)
4. 5 đánh giá (icon cam)

Hover effect: Card nâng lên, shadow tăng
```

**Upcoming Events:**
```
Kiểm tra 2 event cards:
1. Đại nhạc hội Rock Việt 2024
   - Image, title, date, location
   - 2 vé VIP
   - Button "Xem vé" và "Chi tiết"

2. Hội chợ Công nghệ 2024
   - Tương tự
```

**Activity Timeline:**
```
Kiểm tra 3 activity items với icons màu khác nhau:
1. Đặt vé thành công (green check)
2. Đã yêu thích sự kiện (blue heart)
3. Đánh giá sự kiện (yellow star)
```

#### Bước 5: Test Tickets Section

**Ticket Filters:**
```
1. Click "Tất cả (12)" → Hiển thị tất cả ticket cards
2. Click "Sắp tới (3)" → Chỉ hiển thị ticket có class "upcoming"
3. Click "Đã qua (9)" → Chỉ hiển thị ticket có class "past"
```

**Ticket Cards:**
```
Kiểm tra ticket card structure:
- Header: Status badge + Date
- Content: Image + Info
- Details: Calendar, Location, Ticket, Order code
- Footer: Price + Actions

Buttons test:
1. "Xem vé" (QR code) → Chưa có function
2. "Tải về" → Chưa có function
3. "Share" icon → Chưa có function
```

**Responsive:**
```
Mobile (< 768px):
- Image full width
- Actions xếp dọc
- Footer xếp dọc
```

#### Bước 6: Test History Section

**Data Table:**
```
Kiểm tra table với 3 rows:
1. #EVT123456 - Đại nhạc hội - 1.200.000đ
2. #EVT123457 - Hội chợ Công nghệ - 350.000đ
3. #EVT123455 - Workshop UI/UX - 200.000đ

Hover row: Background thay đổi
```

**Pagination:**
```
Bottom: "< Trang 1/2 >"
Prev button disabled (vì đang ở page 1)
Next button enabled
```

#### Bước 7: Test Profile Section

**Avatar Upload:**
```
1. Hover avatar
2. Click camera icon ở góc
3. Kết quả: (Chưa có function, chỉ UI)
```

**Profile Form:**
```
1. Edit các fields:
   - Họ tên, SĐT
   - Email, Ngày sinh
   - Địa chỉ
2. Click "Lưu thay đổi"
3. Kết quả: (Chưa có function, chỉ UI)
```

**Change Password:**
```
Form với 3 fields:
- Mật khẩu hiện tại
- Mật khẩu mới
- Xác nhận mật khẩu mới

Click "Đổi mật khẩu": (Chưa có function)
```

#### Bước 8: Test Settings Section

**Toggle Switches:**
```
Test các toggle switches:

Thông báo:
1. Email thông báo (checked)
2. Nhắc nhở sự kiện (checked)
3. Khuyến mãi đối tác (unchecked)

Quyền riêng tư:
1. Hiển thị hồ sơ (unchecked)
2. Hiển thị sự kiện (unchecked)

Click toggle: Switch animation (slide + color change)
```

**Danger Zone:**
```
1. Scroll xuống "Vùng nguy hiểm"
2. Kiểm tra border màu đỏ
3. Button "Xóa tài khoản" màu đỏ
4. Click: (Chưa có function)
```

---

## 📱 Responsive Testing Checklist

### Desktop (> 992px)
- ✅ Auth aside hiển thị
- ✅ Dashboard sidebar cố định
- ✅ Stats grid 4 columns
- ✅ Form row 2 columns
- ✅ Ticket content flexbox row

### Tablet (768px - 992px)
- ✅ Auth aside ẩn hoặc giảm kích thước
- ✅ Dashboard sidebar toggle
- ✅ Stats grid 2 columns
- ✅ Form row 1 column

### Mobile (< 768px)
- ✅ Auth aside hoàn toàn ẩn
- ✅ Social buttons xếp dọc
- ✅ Dashboard sidebar slide-in
- ✅ User name ẩn trong top nav
- ✅ Stats grid 1 column
- ✅ Ticket image full width
- ✅ Table font size nhỏ hơn

---

## 🐛 Known Issues & Limitations

### Phase 2 Limitations:

1. **No Backend Integration**
   - Login/Register chỉ mô phỏng với localStorage
   - Không có API calls thực tế
   - Data hardcoded

2. **Incomplete Features**
   - Social login buttons chỉ có UI
   - QR code chưa generate
   - Download ticket chưa có
   - Email verification chưa có
   - Forgot password chưa có

3. **Profile Actions**
   - Avatar upload chỉ UI
   - Form save chưa persist
   - Password change chưa validate với current password

4. **Settings**
   - Toggle switches chưa lưu vào database
   - Delete account chưa có confirmation modal

---

## 🎯 Test Summary

### Test Checklist

**Login Page:**
- ✅ UI rendering
- ✅ Email validation
- ✅ Password validation
- ✅ Password toggle
- ✅ Form submission
- ✅ Redirect to dashboard
- ✅ Responsive design

**Register Page:**
- ✅ UI rendering
- ✅ All field validation
- ✅ Password strength indicator
- ✅ Password match
- ✅ Phone validation
- ✅ Terms checkbox
- ✅ Form submission
- ✅ Responsive design

**User Dashboard:**
- ✅ Navigation (top + sidebar)
- ✅ Section switching
- ✅ Stats cards
- ✅ Upcoming events
- ✅ Activity timeline
- ✅ Ticket filtering
- ✅ Order history table
- ✅ Profile form
- ✅ Settings toggles
- ✅ Responsive sidebar
- ✅ Mobile optimizations

---

## 💡 Tips for Testing

### Chrome DevTools
```
F12 → Toggle device toolbar (Ctrl+Shift+M)
Test các breakpoints: 1920px, 1200px, 992px, 768px, 480px, 375px
```

### LocalStorage Inspection
```
F12 → Application → Local Storage
Check 'user' object sau khi login
```

### Console Errors
```
F12 → Console
Kiểm tra không có lỗi JavaScript
```

### Network Tab
```
F12 → Network
Kiểm tra tất cả resources load thành công (CSS, JS, images)
```

---

## 🚀 Next Steps

Sau khi test xong Phase 2, ready cho:
- **Phase 3:** Organizer Dashboard
- **Phase 4:** Admin Panel
- **Phase 5:** Backend Integration
- **Phase 6:** Payment Gateway
- **Phase 7:** Production Deployment

---

**Happy Testing!** 🎉

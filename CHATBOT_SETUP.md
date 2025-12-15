# 🤖 AI Chatbot - Tổng Quan Nhanh

## ✅ Đã Hoàn Thành

### 1. **ChatbotController** (`app/Http/Controllers/ChatbotController.php`)
   - Xử lý logic chatbot
   - Kết nối Google Gemini API
   - Đọc dữ liệu sự kiện từ database
   - 3 methods chính:
     * `index()` - Hiển thị giao diện
     * `sendMessage()` - Xử lý tin nhắn
     * `getSuggestions()` - Gợi ý sự kiện

### 2. **Routes** (`routes/web.php`)
   - `GET /chatbot` - Trang chatbot
   - `POST /chatbot/send` - API gửi tin nhắn
   - `GET /chatbot/suggestions` - API gợi ý

### 3. **Giao Diện** (`resources/views/chatbot/index.blade.php`)
   - Design hiện đại, gradient tím
   - Responsive (mobile + desktop)
   - Realtime typing indicator
   - Quick suggestions buttons
   - Smooth animations

### 4. **Navigation**
   - Thêm link "Trợ Lý AI" vào navbar
   - Icon robot 🤖

### 5. **Environment** (`.env`)
   - Thêm `GEMINI_API_KEY`

---

## 🚀 Cách Sử Dụng

### Bước 1️⃣: Lấy API Key
1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập Google
3. Tạo API Key mới
4. Copy API key

### Bước 2️⃣: Cấu Hình
Mở file `.env`, tìm dòng:
```env
GEMINI_API_KEY=your_gemini_api_key_here
```
Thay `your_gemini_api_key_here` bằng API key thực của bạn.

### Bước 3️⃣: Test
```bash
# Clear cache
php artisan config:clear

# Chạy server
php artisan serve
```

Truy cập: http://localhost:8000/chatbot

---

## 💡 Tính Năng AI

### Chatbot có thể:
✅ Đọc tất cả sự kiện từ database  
✅ Trả lời câu hỏi về:
   - Sự kiện sắp diễn ra
   - Giá vé, loại vé
   - Địa điểm, thời gian
   - Danh mục sự kiện
✅ Gợi ý sự kiện phù hợp  
✅ Tìm kiếm theo yêu cầu  

### Ví dụ câu hỏi:
- "Có sự kiện nào sắp diễn ra không?"
- "Tìm sự kiện âm nhạc cho tôi"
- "Sự kiện nào có vé dưới 500k?"
- "Cho tôi xem các sự kiện cuối tuần"
- "Thông tin về sự kiện [tên]"

---

## 📊 Cách Hoạt Động

```
User nhập câu hỏi
    ↓
ChatbotController nhận request
    ↓
Lấy dữ liệu sự kiện từ DB (20 sự kiện gần nhất)
    ↓
Tạo prompt với context sự kiện
    ↓
Gửi đến Google Gemini API
    ↓
Nhận câu trả lời từ AI
    ↓
Trả về cho user
```

---

## 🎨 Giao Diện

- **Màu chủ đạo**: Gradient tím (#667eea → #764ba2)
- **Typography**: Segoe UI, modern
- **Icons**: Font Awesome 6
- **Effects**: 
  - Fade in animation
  - Typing indicator
  - Smooth scrolling
  - Hover effects

---

## ⚙️ Tùy Chỉnh

### Thay đổi số sự kiện load:
File: `ChatbotController.php`, dòng 76
```php
->take(20) // Đổi số này
```

### Điều chỉnh AI:
File: `ChatbotController.php`, dòng 52-53
```php
'temperature' => 0.7,      // 0-1: độ sáng tạo
'maxOutputTokens' => 800,  // độ dài câu trả lời
```

### Thay đổi prompt:
File: `ChatbotController.php`, hàm `buildPrompt()`

---

## 📁 Cấu Trúc Files

```
app/Http/Controllers/
  └── ChatbotController.php        ← Logic chính

resources/views/chatbot/
  └── index.blade.php               ← Giao diện

routes/
  └── web.php                       ← Routes

.env                                ← API Key
```

---

## 🔒 Bảo Mật

⚠️ **LƯU Ý:**
- **KHÔNG** commit file `.env`
- **KHÔNG** chia sẻ API key
- Sử dụng `.gitignore` để bảo vệ

---

## 🐛 Troubleshooting

### API Key không hoạt động?
```bash
php artisan config:clear
php artisan cache:clear
```

### Chatbot không phản hồi?
- Kiểm tra API key trong `.env`
- Xem log: `storage/logs/laravel.log`
- Kiểm tra internet connection

### Lỗi CORS?
- Đảm bảo đã có Guzzle HTTP client
- Laravel đã tích hợp sẵn

---

## 📚 Tài Liệu

- **Google Gemini API**: https://ai.google.dev/docs
- **Laravel HTTP Client**: https://laravel.com/docs/http-client
- **File README chi tiết**: `CHATBOT_AI.md`

---

## 🎯 Next Steps

Có thể mở rộng:
1. ✨ Lưu lịch sử chat vào DB
2. 🎤 Thêm voice input (speech-to-text)
3. 🌍 Đa ngôn ngữ (EN/VI)
4. 📊 Analytics (tracking câu hỏi phổ biến)
5. 🎨 Theme switcher (dark/light mode)

---

**Chúc bạn thành công! 🚀**

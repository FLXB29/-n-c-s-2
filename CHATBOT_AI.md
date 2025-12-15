# AI Chatbot - Hướng Dẫn Cài Đặt

## Tính Năng
✅ Chatbot AI thông minh sử dụng Google Gemini API
✅ Trả lời câu hỏi về sự kiện từ database
✅ Gợi ý sự kiện phù hợp với nhu cầu người dùng
✅ Hiển thị thông tin chi tiết: giá vé, thời gian, địa điểm
✅ Giao diện chat hiện đại, responsive

## Cài Đặt

### Bước 1: Lấy API Key từ Google AI Studio
1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập bằng tài khoản Google
3. Click "Create API Key"
4. Copy API key vừa tạo

### Bước 2: Cấu Hình Môi Trường
Mở file `.env` và thêm API key:
```env
GEMINI_API_KEY=your_actual_api_key_here
```

### Bước 3: Truy Cập Chatbot
Mở trình duyệt và truy cập:
```
http://localhost:8000/chatbot
```

## Cách Sử Dụng

### Ví Dụ Câu Hỏi:
- "Có sự kiện nào sắp diễn ra không?"
- "Tìm sự kiện âm nhạc cho tôi"
- "Sự kiện nào có vé rẻ nhất?"
- "Cho tôi xem các sự kiện cuối tuần"
- "Thông tin về sự kiện [tên sự kiện]"
- "Sự kiện nào ở [địa điểm]?"

### Tính Năng Đặc Biệt:
- 🤖 AI tự động đọc dữ liệu sự kiện từ database
- 💬 Trả lời thông minh dựa trên context
- 🎯 Gợi ý sự kiện phù hợp
- ⚡ Realtime typing indicator
- 📱 Responsive design

## Cấu Trúc File

```
app/Http/Controllers/
  └── ChatbotController.php      # Controller xử lý logic chatbot

resources/views/chatbot/
  └── index.blade.php             # Giao diện chat

routes/
  └── web.php                     # Định nghĩa routes
```

## API Endpoints

### 1. Hiển thị chatbot
```
GET /chatbot
```

### 2. Gửi tin nhắn
```
POST /chatbot/send
Body: { message: "câu hỏi của bạn" }
Response: { success: true, message: "câu trả lời" }
```

### 3. Lấy gợi ý sự kiện
```
GET /chatbot/suggestions?query=search_term
Response: { success: true, events: [...] }
```

## Cấu Hình Nâng Cao

### Tùy Chỉnh Prompt AI
Mở `ChatbotController.php` và chỉnh sửa hàm `buildPrompt()`:
```php
private function buildPrompt($userMessage, $eventsContext)
{
    return <<<PROMPT
    // Tùy chỉnh prompt của bạn ở đây
    PROMPT;
}
```

### Thay Đổi Số Lượng Sự Kiện Load
Trong hàm `getEventsContext()`, thay đổi:
```php
->take(20) // Số sự kiện load vào context
```

### Điều Chỉnh Độ Sáng Tạo AI
Trong hàm `sendMessage()`, thay đổi:
```php
'temperature' => 0.7, // 0.0 = conservative, 1.0 = creative
'maxOutputTokens' => 800, // Độ dài tối đa câu trả lời
```

## Troubleshooting

### Lỗi: "API key not valid"
- Kiểm tra lại API key trong file `.env`
- Đảm bảo API key được tạo từ Google AI Studio
- Xóa cache: `php artisan config:clear`

### Lỗi: "Could not connect to AI"
- Kiểm tra kết nối internet
- Kiểm tra firewall/proxy
- Xem log: `storage/logs/laravel.log`

### Chatbot không hiểu câu hỏi
- Thử đặt câu hỏi rõ ràng hơn
- Sử dụng các gợi ý có sẵn
- Kiểm tra dữ liệu sự kiện trong database

## Bảo Mật

⚠️ **QUAN TRỌNG:**
- KHÔNG commit file `.env` lên Git
- KHÔNG chia sẻ API key với người khác
- Sử dụng `.env.example` để hướng dẫn cấu hình

## Giới Hạn

- Gemini API có giới hạn request miễn phí
- Mỗi request tối đa 800 tokens output
- Context giới hạn 20 sự kiện gần nhất

## Nâng Cấp

### Thêm tính năng lưu lịch sử chat:
1. Tạo migration cho bảng `chatbot_conversations`
2. Lưu tin nhắn vào database
3. Load lịch sử khi user quay lại

### Tích hợp voice input:
1. Sử dụng Web Speech API
2. Convert speech to text
3. Gửi text đến chatbot

### Đa ngôn ngữ:
1. Thêm language detector
2. Thay đổi prompt theo ngôn ngữ
3. Translate responses

## Liên Hệ & Hỗ Trợ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra file log: `storage/logs/laravel.log`
2. Đọc tài liệu Google Gemini API
3. Liên hệ team phát triển

---

**Phiên bản:** 1.0.0  
**Ngày cập nhật:** {{ date('d/m/Y') }}  
**Framework:** Laravel 9.x + Google Gemini API

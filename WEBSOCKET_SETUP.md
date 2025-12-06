# HƯỚNG DẪN SỬ DỤNG CHAT VỚI LARAVEL WEBSOCKETS

## Các bước đã hoàn thành:

### 1. Cài đặt và cấu hình
✅ Migration bảng messages đã chạy
✅ Laravel WebSockets đã cấu hình
✅ Laravel Echo đã được kích hoạt trong bootstrap.js
✅ Vite build assets thành công

### 2. Khởi động hệ thống

**Bước 1: Chạy WebSocket Server (Terminal 1)**
```bash
php artisan websockets:serve
```
Server sẽ chạy trên port 6001

**Bước 2: Chạy Laravel Application (Terminal 2)**
```bash
php artisan serve
```
App sẽ chạy trên http://127.0.0.1:8000

**Bước 3 (Tùy chọn): Chạy Vite dev server (Terminal 3)**
```bash
npm run dev
```
Để hot-reload JavaScript trong quá trình development

### 3. Kiểm tra WebSocket Dashboard

Truy cập: http://127.0.0.1:8000/laravel-websockets

Tại đây bạn có thể:
- Xem các connections đang hoạt động
- Monitor messages được broadcast
- Debug real-time events

### 4. Sử dụng Chat

#### Với User/Organizer:
1. Đăng nhập vào hệ thống
2. Click icon Chat (💬) trên navbar
3. Gửi tin nhắn cho Admin
4. Nhận tin nhắn real-time từ Admin

#### Với Admin:
1. Đăng nhập với tài khoản admin
2. Truy cập /chat
3. Chọn user từ sidebar bên trái
4. Chat với user đã chọn
5. Xem danh sách tất cả conversations

### 5. Tính năng Real-time

**Broadcasting được kích hoạt qua:**
- Laravel WebSockets (port 6001)
- Laravel Echo (Pusher protocol)
- Private channels: `chat.{userId}`

**Event được broadcast:**
- `MessageSent` - Khi tin nhắn mới được gửi
- Channel: `chat.{sender_id}` và `chat.{receiver_id}`

### 6. Cấu trúc .env

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=eventhub-local
PUSHER_APP_KEY=eventhub-key
PUSHER_APP_SECRET=eventhub-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

### 7. Fallback System

Nếu WebSocket không hoạt động:
- Hệ thống tự động polling mỗi 10 giây
- Vẫn có thể gửi/nhận tin nhắn
- Chỉ thiếu tính năng real-time

### 8. Debugging

**Kiểm tra Console Log:**
```javascript
// Mở DevTools > Console
// Xem logs:
- "Laravel Echo initialized successfully" ✅
- "New message received via Echo:" ✅
```

**Kiểm tra Network:**
- Xem WebSocket connection trong Network tab
- ws://127.0.0.1:6001/... phải có status "101 Switching Protocols"

**Kiểm tra Laravel WebSockets Dashboard:**
- http://127.0.0.1:8000/laravel-websockets
- Xem connections và statistics

### 9. Troubleshooting

**Lỗi: WebSocket connection failed**
- Kiểm tra `php artisan websockets:serve` có đang chạy
- Kiểm tra port 6001 có bị chiếm không
- Restart WebSocket server

**Lỗi: Echo is undefined**
- Chạy `npm run build` để build lại assets
- Clear cache: `php artisan optimize:clear`
- Hard refresh browser (Ctrl + Shift + R)

**Lỗi: Messages không gửi được**
- Kiểm tra CSRF token
- Kiểm tra user đã login chưa
- Xem Console log có lỗi không

**Lỗi: Không thấy tin nhắn real-time**
- Kiểm tra WebSocket Dashboard xem có connections không
- Kiểm tra Event có được broadcast không
- Xem Console log có nhận được event không

### 10. Development vs Production

**Development:**
```bash
# Terminal 1
php artisan websockets:serve

# Terminal 2
php artisan serve

# Terminal 3
npm run dev
```

**Production:**
```bash
# Build assets
npm run build

# Run WebSocket as daemon (supervisor/pm2)
php artisan websockets:serve

# Use Nginx/Apache instead of artisan serve
```

### 11. Security Notes

- ✅ User/Organizer chỉ chat được với Admin
- ✅ Admin chat được với tất cả
- ✅ Private channels với authorization
- ✅ CSRF protection
- ✅ Authentication middleware

### 12. Performance Tips

- Sử dụng Redis queue cho production
- Enable statistics trong WebSocket config
- Monitor connections và resources
- Set proper limits trong config/websockets.php

## Commands Tóm Tắt

```bash
# Start WebSocket
php artisan websockets:serve

# Start Laravel
php artisan serve

# Build assets
npm run build

# Watch for changes (dev)
npm run dev

# Clear cache
php artisan optimize:clear

# Check routes
php artisan route:list | grep chat
```

## Kết quả mong đợi

✅ WebSocket server chạy trên port 6001
✅ Chat interface hiển thị đẹp
✅ Gửi tin nhắn thành công
✅ Nhận tin nhắn real-time
✅ Hiển thị số tin nhắn chưa đọc
✅ Admin xem được danh sách conversations

# Hướng dẫn sử dụng tính năng Chat

## Tổng quan
Tính năng chat cho phép user và organizer chat trực tiếp với admin. Chỉ admin mới có thể chat với nhiều người, còn user/organizer chỉ có thể chat với admin.

## Cài đặt

### 1. Chạy Migration
```bash
php artisan migrate --path=database/migrations/2025_12_06_090949_create_messages_table.php
```

### 2. Đảm bảo WebSockets đang chạy
```bash
php artisan websockets:serve
```

### 3. Cấu hình Broadcasting (nếu chưa có)
Kiểm tra file `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=local
PUSHER_APP_KEY=local
PUSHER_APP_SECRET=local
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

## Cấu trúc Files

### Backend
- **Migration**: `database/migrations/2025_12_06_090949_create_messages_table.php`
- **Model**: `app/Models/Message.php`
- **Controller**: `app/Http/Controllers/ChatController.php`
- **Event**: `app/Events/MessageSent.php`
- **Routes**: `routes/web.php` và `routes/channels.php`

### Frontend
- **View**: `resources/views/chat/index.blade.php`
- **JavaScript**: `public/js/chat.js`
- **CSS**: `public/css/chat.css`

## Tính năng

### Cho User/Organizer:
- Xem giao diện chat với admin
- Gửi tin nhắn cho admin
- Nhận tin nhắn từ admin real-time
- Xem lịch sử chat
- Hiển thị số tin nhắn chưa đọc

### Cho Admin:
- Xem danh sách hội thoại với tất cả users/organizers
- Chọn người dùng để chat
- Gửi và nhận tin nhắn real-time
- Xem số tin nhắn chưa đọc từ mỗi người
- Quản lý nhiều cuộc hội thoại

## Routes

### Web Routes
```
GET  /chat                      - Trang chat chính
GET  /chat/messages/{userId}    - Lấy tin nhắn với user
POST /chat/send                 - Gửi tin nhắn
GET  /chat/unread-count         - Số tin nhắn chưa đọc
PATCH /chat/messages/{id}/read  - Đánh dấu đã đọc
GET  /chat/conversations        - Danh sách hội thoại (admin)
```

### Broadcast Channels
```
chat.{userId} - Private channel cho mỗi user
```

## API Endpoints

### GET /chat/messages/{userId}
Lấy tin nhắn giữa current user và userId
```json
Response: [
  {
    "id": 1,
    "sender_id": 1,
    "receiver_id": 2,
    "message": "Hello",
    "is_read": false,
    "created_at": "2025-12-06T10:00:00",
    "sender": {
      "id": 1,
      "name": "User Name"
    }
  }
]
```

### POST /chat/send
Gửi tin nhắn mới
```json
Request: {
  "receiver_id": 2,
  "message": "Hello admin"
}

Response: {
  "id": 1,
  "sender_id": 1,
  "receiver_id": 2,
  "message": "Hello admin",
  "is_read": false,
  "created_at": "2025-12-06T10:00:00"
}
```

### GET /chat/unread-count
Lấy số tin nhắn chưa đọc
```json
Response: {
  "count": 5
}
```

## Bảo mật

### Kiểm tra trong ChatController:
1. **User/Organizer chỉ chat được với Admin**
   ```php
   if ($currentUser->role !== 'admin' && $userId != $admin->id) {
       return response()->json(['error' => 'Bạn chỉ có thể chat với admin.'], 403);
   }
   ```

2. **Admin có thể chat với bất kỳ ai**
   ```php
   if ($currentUser->role === 'admin' && !User::find($userId)) {
       return response()->json(['error' => 'User không tồn tại.'], 404);
   }
   ```

## Real-time Broadcasting

Khi tin nhắn được gửi:
1. Message được lưu vào database
2. Event `MessageSent` được broadcast
3. Pusher gửi event đến private channels của sender và receiver
4. JavaScript nhận event và cập nhật UI

## Giao diện

### Desktop
- Sidebar bên trái hiển thị conversations (chỉ admin)
- Chat area chính ở giữa
- Input box ở dưới cùng

### Mobile
- Responsive design
- Full width chat area
- Collapsible sidebar (admin)

## Customization

### Thay đổi màu sắc
Chỉnh trong `public/css/chat.css`:
```css
:root {
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --secondary-color: #8b5cf6;
}
```

### Thay đổi giới hạn ký tự
Trong `resources/views/chat/index.blade.php`:
```html
<textarea maxlength="1000">
```

### Thay đổi tần suất polling
Trong `public/js/chat.js`:
```javascript
messageCheckInterval = setInterval(() => {
    // ...
}, 5000); // 5 giây
```

## Testing

### 1. Tạo admin user
```php
php artisan tinker
$admin = User::find(1);
$admin->role = 'admin';
$admin->save();
```

### 2. Test chat
1. Đăng nhập với user/organizer account
2. Vào `/chat`
3. Gửi tin nhắn cho admin
4. Đăng nhập với admin account
5. Vào `/chat` và kiểm tra tin nhắn

## Troubleshooting

### Tin nhắn không gửi được
- Kiểm tra CSRF token trong meta tag
- Kiểm tra route có được định nghĩa chưa
- Xem Console log để debug

### Real-time không hoạt động
- Kiểm tra WebSocket server đang chạy
- Kiểm tra Echo configuration trong resources/js/app.js
- Xem Pusher logs

### Database lỗi
- Chạy `php artisan migrate:fresh` (cảnh báo: sẽ xóa toàn bộ data)
- Hoặc chỉ rollback và migrate lại bảng messages

## Notes

- Admin phải tồn tại trong hệ thống (role = 'admin')
- WebSockets cần chạy để real-time hoạt động
- Nếu không có WebSocket, hệ thống vẫn hoạt động với polling (5s/lần)

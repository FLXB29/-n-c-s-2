# HƯỚNG DẪN TÍNH NĂNG CHAT - CẬP NHẬT CONVERSATIONS

## Thay đổi mới nhất:

### 1. Logic Conversations cho Admin

**Trước đây:**
- Admin không thấy danh sách người dùng
- Phải biết trước user để chat

**Bây giờ:**
- ✅ Khi user/organizer gửi tin nhắn đầu tiên → Tự động xuất hiện trong danh sách của admin
- ✅ Danh sách được sắp xếp theo thời gian tin nhắn gần nhất
- ✅ Hiển thị preview tin nhắn cuối cùng
- ✅ Hiển thị thời gian (vừa xong, 5 phút, 2 giờ, 3 ngày...)
- ✅ Hiển thị số tin nhắn chưa đọc
- ✅ Tự động refresh khi có tin nhắn mới

### 2. Cách hoạt động

#### Bước 1: User gửi tin nhắn đầu tiên
```
User A (role: user) → Gửi "Xin chào Admin" → Admin
```

#### Bước 2: Admin nhận notification
- WebSocket broadcast event `MessageSent`
- Admin nhận event real-time
- Danh sách conversations tự động refresh

#### Bước 3: User A xuất hiện trong sidebar
```
Conversations List (Admin):
┌─────────────────────────────────┐
│ 👤 User A          Vừa xong ⓪  │
│ "Xin chào Admin"                │
│ [Người dùng]                    │
└─────────────────────────────────┘
```

#### Bước 4: Admin click vào User A
- Load lịch sử chat
- Enable input để reply
- Đánh dấu tin nhắn đã đọc

### 3. API Endpoint Changes

**GET /chat/conversations**

Response trước:
```json
[
  {
    "id": 2,
    "name": "User A",
    "email": "usera@example.com",
    "role": "user",
    "unread_count": 1
  }
]
```

Response bây giờ:
```json
[
  {
    "id": 2,
    "name": "User A",
    "email": "usera@example.com",
    "role": "user",
    "unread_count": 1,
    "last_message": {
      "id": 15,
      "message": "Xin chào Admin",
      "created_at": "2025-12-06T15:30:00"
    },
    "last_message_time": "2025-12-06T15:30:00"
  }
]
```

### 4. UI Improvements

#### Conversation Item - Trước:
```
👤 User A
   usera@example.com
   [Người dùng]          ①
```

#### Conversation Item - Bây giờ:
```
👤 User A              Vừa xong
   "Xin chào Admin"
   [Người dùng]                  ①
```

### 5. Real-time Updates

**Khi có tin nhắn mới:**
1. Laravel broadcasts `MessageSent` event
2. Echo listener nhận event
3. Nếu là admin:
   - Tự động refresh conversations
   - Cập nhật vị trí trong danh sách
   - Update unread count
4. Nếu tin nhắn từ người đang chat:
   - Append vào chat window
   - Scroll to bottom

**Polling fallback (mỗi 10 giây):**
- Load messages mới (silent)
- Update unread count
- Admin: Refresh conversations

### 6. Time Format

```javascript
function formatTime(datetime) {
    < 1 phút    → "Vừa xong"
    < 60 phút   → "5 phút", "15 phút"
    < 24 giờ    → "2 giờ", "12 giờ"
    < 7 ngày    → "1 ngày", "5 ngày"
    >= 7 ngày   → "06/12", "25/11"
}
```

### 7. Testing Flow

#### Test 1: User gửi tin nhắn đầu tiên
```bash
# Terminal 1: WebSocket
php artisan websockets:serve

# Terminal 2: App
php artisan serve

# Browser 1: User login
- Truy cập: http://127.0.0.1:8000/chat
- Gửi: "Hello Admin"

# Browser 2: Admin login
- Truy cập: http://127.0.0.1:8000/chat
- Xem sidebar → User xuất hiện
- Click vào User → Xem tin nhắn
- Reply: "Hi, how can I help?"

# Browser 1: User
- Nhận tin nhắn real-time
```

#### Test 2: Multiple users chat
```bash
# Browser 1: User A
- Gửi: "Question 1"

# Browser 2: User B
- Gửi: "Question 2"

# Browser 3: Admin
- Sidebar hiển thị cả User A và User B
- User B ở trên (tin nhắn mới hơn)
- Click User B → Chat với User B
- Click User A → Chat với User A
```

### 8. Database Query

**Controller: ChatController@getConversations**

```php
// Lấy IDs của người gửi tin nhắn cho admin
$senderIds = Message::where('receiver_id', $admin->id)
    ->distinct()
    ->pluck('sender_id');

// Lấy IDs của người nhận tin nhắn từ admin
$receiverIds = Message::where('sender_id', $admin->id)
    ->distinct()
    ->pluck('receiver_id');

// Merge và lấy unique IDs
$userIds = $senderIds->merge($receiverIds)->unique();

// Lấy thông tin users với last message
User::whereIn('id', $userIds)
    ->get()
    ->map(function($user) {
        // Thêm last_message, last_message_time, unread_count
        return $user;
    })
    ->sortByDesc('last_message_time');
```

### 9. CSS Updates

```css
.conversation-header {
    display: flex;
    justify-content: space-between;
}

.conversation-time {
    font-size: 11px;
    color: #9ca3af;
}

.conversation-preview {
    font-size: 13px;
    color: #6b7280;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
```

### 10. Troubleshooting

**Q: Admin không thấy user sau khi user gửi tin nhắn**
```bash
# Check 1: WebSocket có chạy không?
php artisan websockets:serve

# Check 2: Echo có load không?
# Browser Console → Kiểm tra "Laravel Echo initialized"

# Check 3: Event có được broadcast không?
# http://127.0.0.1:8000/laravel-websockets
# Xem connections và messages

# Check 4: Reload conversations manually
# Click nút refresh (⟳) trên sidebar
```

**Q: Danh sách không sort theo thời gian**
```bash
# Check API response
GET /chat/conversations
# Kiểm tra last_message_time có đúng không
```

**Q: Preview tin nhắn không hiển thị**
```bash
# Check API response có last_message không
# Check JavaScript console có lỗi không
```

### 11. Next Steps

Có thể thêm:
- [ ] Typing indicator (đang nhập...)
- [ ] Online/Offline status
- [ ] File/Image upload
- [ ] Delete messages
- [ ] Search conversations
- [ ] Pin important conversations
- [ ] Mute notifications

### 12. Code Files Changed

```
✅ app/Http/Controllers/ChatController.php
   - getConversations() method updated
   
✅ resources/js/chat.js
   - displayConversations() updated
   - formatTime() added
   - Echo listener improved
   
✅ public/css/chat.css
   - conversation-header added
   - conversation-time added
   - conversation-preview added
```

## Kết quả

✅ User gửi tin nhắn → Xuất hiện trong danh sách admin
✅ Danh sách sort theo thời gian mới nhất
✅ Hiển thị preview tin nhắn cuối
✅ Real-time updates qua WebSocket
✅ Fallback polling 10 giây

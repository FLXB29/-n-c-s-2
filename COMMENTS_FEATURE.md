# Tính Năng Bình Luận Sự Kiện (Event Comments)

## Mô Tả
Hệ thống cho phép người dùng bình luận, đánh giá sao cho các sự kiện. Bình luận sẽ được duyệt trước khi hiển thị.

## Cấu Trúc

### 1. Database
- **Bảng:** `event_comments`
- **Các cột:**
  - `id` - ID bình luận
  - `event_id` - ID sự kiện (khóa ngoại)
  - `user_id` - ID người dùng (khóa ngoại)
  - `parent_id` - ID bình luận cha (cho reply)
  - `content` - Nội dung bình luận
  - `rating` - Đánh giá sao (1-5)
  - `status` - Trạng thái (pending, approved, rejected)
  - `created_at`, `updated_at` - Thời gian

### 2. Models
#### Comment Model (`app/Models/Comment.php`)
```php
- relationships:
  - event() - Sự kiện
  - user() - Người bình luận
  - parent() - Bình luận cha (reply)
  - replies() - Các bình luận trả lời

- scopes:
  - approved() - Chỉ bình luận được duyệt
  - pending() - Chỉ bình luận chờ duyệt
```

#### Event Model
Thêm relationships:
- `comments()` - Các bình luận chính (không phải reply)
- `allComments()` - Tất cả bình luận

### 3. Controllers
#### CommentController (`app/Http/Controllers/CommentController.php`)
- `store()` - Tạo bình luận mới
- `update()` - Sửa bình luận
- `destroy()` - Xóa bình luận
- `approve()` - Duyệt bình luận (admin/organizer)
- `reject()` - Từ chối bình luận (admin/organizer)

### 4. Routes
```php
// Public routes (phải đăng nhập)
POST /events/{event}/comments - Tạo bình luận
PATCH /comments/{comment} - Cập nhật bình luận
DELETE /comments/{comment} - Xóa bình luận

// Admin routes
PATCH /admin/comments/{comment}/approve - Duyệt bình luận
PATCH /admin/comments/{comment}/reject - Từ chối bình luận
```

### 5. Views
#### Component: `resources/views/components/event-comments.blade.php`
Hiển thị:
- Form thêm bình luận
- Danh sách bình luận đã được duyệt
- Form chỉnh sửa bình luận
- Modal để sửa bình luận

#### CSS: `public/css/comments.css`
Các style cho comments component

### 6. Migrations
- `2025_12_06_000000_create_event_comments_table.php` - Tạo bảng event_comments

## Cách Sử Dụng

### Trong View
```blade
<!-- Hiển thị section comments -->
@include('components.event-comments', ['comments' => $comments])
```

### EventController - Method show()
```php
public function show($id)
{
    $event = Event::where('id', $id)->orWhere('slug', $id)->firstOrFail();
    $event->increment('view_count');
    
    $event->load(['category', 'organizer', 'ticketTypes' => ...]);
    
    // Load approved comments
    $comments = $event->comments()
        ->approved()
        ->with('user', 'replies.user')
        ->orderByDesc('created_at')
        ->get();
    
    return view('events.show', compact('event', 'comments'));
}
```

## Tính Năng Chi Tiết

### 1. Tạo Bình Luận
- Người dùng phải đăng nhập
- Sự kiện phải cho phép comments (cột `allow_comments` = true)
- Có thể thêm nội dung và đánh giá sao (1-5)
- Bình luận chờ duyệt (status = pending)

### 2. Xem Bình Luận
- Chỉ hiển thị bình luận được duyệt (status = approved)
- Hiển thị theo thứ tự mới nhất trước
- Hiển thị thông tin người bình luận (avatar, tên, thời gian)
- Hiển thị đánh giá sao nếu có

### 3. Sửa Bình Luận
- Chỉ có quyền sửa bình luận của chính mình hoặc admin
- Có thể sửa nội dung và đánh giá sao
- Sửa xong không cần duyệt lại

### 4. Xóa Bình Luận
- Chỉ có quyền xóa bình luận của chính mình hoặc admin
- Khi xóa bình luận cha, các reply cũng bị xóa

### 5. Duyệt Bình Luận
- Admin hoặc organizer có thể duyệt/từ chối bình luận
- Bình luận được duyệt sẽ hiển thị cho tất cả người dùng

## Quyền Hạn

| Hành Động | Người Dùng | Organizer | Admin |
|-----------|-----------|-----------|-------|
| Tạo bình luận | ✓ | ✓ | ✓ |
| Sửa bình luận của mình | ✓ | ✓ | ✓ |
| Xóa bình luận của mình | ✓ | ✓ | ✓ |
| Duyệt bình luận | ✗ | ✓* | ✓ |
| Từ chối bình luận | ✗ | ✓* | ✓ |

*Organizer chỉ có quyền duyệt/từ chối bình luận cho sự kiện của mình

## Kiểm Tra Sự Kiện Cho Phép Comments
Cột `allow_comments` trong bảng `events` kiểm soát xem sự kiện có cho phép bình luận hay không.

```php
// Enable comments
$event->update(['allow_comments' => true]);

// Disable comments
$event->update(['allow_comments' => false]);
```

## Để Thêm / Sửa Sự Kiện Cho Phép Comments
Vào mục quản lý sự kiện của organizer, sẽ có option enable/disable comments.

## Testing
1. Đăng nhập vào tài khoản người dùng
2. Vào trang chi tiết sự kiện
3. Scroll đến phần "Bình luận sự kiện"
4. Điền bình luận và đánh giá
5. Nhấn "Gửi bình luận"
6. Bình luận sẽ hiển thị trạng thái "Chờ duyệt"
7. Đăng nhập vào tài khoản admin/organizer
8. Vào trang quản lý sự kiện
9. Duyệt hoặc từ chối bình luận
10. Sau khi duyệt, bình luận sẽ hiển thị public

## API Response Format
Bình luận được trả về dưới dạng:
```json
{
  "id": 1,
  "event_id": 5,
  "user_id": 2,
  "content": "Sự kiện tuyệt vời!",
  "rating": 5,
  "status": "approved",
  "user": {
    "id": 2,
    "full_name": "Người Dùng",
    "avatar": "url/to/avatar"
  },
  "created_at": "2025-12-06T10:30:00Z",
  "updated_at": "2025-12-06T10:30:00Z"
}
```

## Lưu Ý
- Không hỗ trợ reply comments lồng nhau quá sâu
- Bình luận không bị xóa mềm, xóa trực tiếp
- Không có notification cho organizer khi có bình luận mới
- Không có moderation tự động

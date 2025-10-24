# Database Schema - EventHub

## Thiết kế cơ sở dữ liệu đề xuất

Đây là schema đề xuất khi tích hợp backend và database vào project.

## 📊 Tables Overview

```
users
├── user_roles (junction table)
├── events (created_by)
├── tickets (purchased_by)
├── comments
└── event_views

categories
└── events

events
├── event_tickets (types)
├── event_seats
├── tickets (sold)
├── comments
└── event_views

tickets
└── check_ins

transactions
└── tickets
```

---

## 📋 Detailed Schema

### 1. `users` table
Lưu thông tin người dùng (cả user thường và organizer)

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar_url VARCHAR(500),
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status)
);
```

### 2. `roles` table
Phân quyền: user, organizer, admin

```sql
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    permissions JSON
);

-- Sample data:
INSERT INTO roles (name, description) VALUES 
('user', 'Người dùng thông thường'),
('organizer', 'Nhà tổ chức sự kiện'),
('admin', 'Quản trị viên');
```

### 3. `user_roles` table (Many-to-Many)
Một user có thể có nhiều role

```sql
CREATE TABLE user_roles (
    user_id INT,
    role_id INT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

### 4. `categories` table
Danh mục sự kiện

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    event_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug)
);

-- Sample data:
INSERT INTO categories (name, slug, icon) VALUES 
('Âm nhạc', 'music', '🎵'),
('Thể thao', 'sports', '⚽'),
('Workshop', 'workshop', '🛠️'),
('Hội thảo', 'conference', '💼'),
('Lễ hội', 'festival', '🎉'),
('Nghệ thuật', 'art', '🎨');
```

### 5. `events` table
Thông tin sự kiện chính

```sql
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    category_id INT,
    
    -- Date & Time
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    duration_minutes INT,
    
    -- Location
    location_name VARCHAR(255) NOT NULL,
    location_address TEXT,
    location_city VARCHAR(100),
    location_lat DECIMAL(10, 8),
    location_lng DECIMAL(11, 8),
    
    -- Capacity
    total_seats INT NOT NULL,
    available_seats INT NOT NULL,
    
    -- Images
    cover_image_url VARCHAR(500),
    images JSON,
    
    -- Organizer
    created_by INT NOT NULL,
    
    -- Status
    status ENUM('draft', 'published', 'cancelled', 'completed') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    
    -- Stats
    view_count INT DEFAULT 0,
    ticket_sold_count INT DEFAULT 0,
    
    -- SEO
    meta_title VARCHAR(255),
    meta_description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_event_date (event_date),
    INDEX idx_category (category_id),
    INDEX idx_featured (is_featured),
    INDEX idx_city (location_city)
);
```

### 6. `event_tickets` table
Các loại vé cho mỗi sự kiện

```sql
CREATE TABLE event_tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    ticket_type VARCHAR(50) NOT NULL, -- 'regular', 'vip', 'svip'
    ticket_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    sold_quantity INT DEFAULT 0,
    description TEXT,
    benefits JSON,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    
    INDEX idx_event (event_id),
    INDEX idx_type (ticket_type)
);
```

### 7. `event_seats` table
Sơ đồ chỗ ngồi (nếu có)

```sql
CREATE TABLE event_seats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    seat_row VARCHAR(10) NOT NULL, -- 'A', 'B', 'C'...
    seat_number INT NOT NULL,
    seat_type VARCHAR(50), -- 'regular', 'vip', 'svip'
    status ENUM('available', 'reserved', 'sold') DEFAULT 'available',
    
    -- Temporary reservation (5-10 minutes)
    reserved_by INT NULL,
    reserved_until TIMESTAMP NULL,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (reserved_by) REFERENCES users(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_seat (event_id, seat_row, seat_number),
    INDEX idx_event (event_id),
    INDEX idx_status (status)
);
```

### 8. `tickets` table
Vé đã bán

```sql
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_code VARCHAR(100) UNIQUE NOT NULL, -- QR code value
    event_id INT NOT NULL,
    event_ticket_id INT NOT NULL,
    
    purchased_by INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Seat info (if applicable)
    seat_id INT NULL,
    
    -- QR Code
    qr_code_url VARCHAR(500),
    
    -- Status
    status ENUM('valid', 'used', 'cancelled', 'refunded') DEFAULT 'valid',
    
    -- Check-in
    checked_in_at TIMESTAMP NULL,
    checked_in_by INT NULL,
    
    -- Price at time of purchase
    price_paid DECIMAL(10, 2) NOT NULL,
    
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (event_ticket_id) REFERENCES event_tickets(id),
    FOREIGN KEY (purchased_by) REFERENCES users(id),
    FOREIGN KEY (seat_id) REFERENCES event_seats(id),
    FOREIGN KEY (checked_in_by) REFERENCES users(id),
    
    INDEX idx_ticket_code (ticket_code),
    INDEX idx_event (event_id),
    INDEX idx_user (purchased_by),
    INDEX idx_status (status)
);
```

### 9. `transactions` table
Lịch sử giao dịch

```sql
CREATE TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    
    -- Payment info
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50), -- 'card', 'momo', 'banking'
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    
    -- External payment gateway
    payment_gateway_transaction_id VARCHAR(255),
    payment_gateway_response JSON,
    
    -- Tickets
    ticket_quantity INT NOT NULL,
    ticket_ids JSON, -- Array of ticket IDs
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id),
    
    INDEX idx_user (user_id),
    INDEX idx_event (event_id),
    INDEX idx_status (payment_status)
);
```

### 10. `comments` table
Bình luận và đánh giá

```sql
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    rating INT NULL, -- 1-5 stars (optional)
    
    -- Reply to another comment
    parent_comment_id INT NULL,
    
    -- Likes
    likes_count INT DEFAULT 0,
    
    status ENUM('visible', 'hidden', 'reported') DEFAULT 'visible',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    
    INDEX idx_event (event_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_comment_id)
);
```

### 11. `comment_likes` table
Người dùng thích comment nào

```sql
CREATE TABLE comment_likes (
    comment_id INT,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (comment_id, user_id),
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 12. `event_views` table
Tracking lượt xem

```sql
CREATE TABLE event_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    user_id INT NULL, -- NULL if guest
    ip_address VARCHAR(45),
    user_agent TEXT,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_event (event_id),
    INDEX idx_date (viewed_at)
);
```

---

## 🔐 Security Considerations

### 1. Password Hashing
```php
// PHP example
$password_hash = password_hash($password, PASSWORD_BCRYPT);
```

### 2. Prevent SQL Injection
- Sử dụng Prepared Statements
- Sanitize user inputs
- Use ORM (Eloquent, Doctrine, etc.)

### 3. XSS Prevention
- Escape output: `htmlspecialchars()`, `strip_tags()`
- Content Security Policy headers

### 4. CSRF Protection
- CSRF tokens for forms
- SameSite cookies

---

## 📈 Indexes for Performance

Đã thêm indexes cho:
- Foreign keys
- Frequently searched columns
- Status/enum columns
- Date columns
- Unique constraints

---

## 🔄 Sample Queries

### Get featured events with category
```sql
SELECT e.*, c.name as category_name, c.icon as category_icon
FROM events e
JOIN categories c ON e.category_id = c.id
WHERE e.status = 'published' 
  AND e.is_featured = TRUE
  AND e.event_date >= CURDATE()
ORDER BY e.event_date ASC
LIMIT 6;
```

### Get user's tickets
```sql
SELECT t.*, e.title, e.event_date, e.location_name, et.ticket_name
FROM tickets t
JOIN events e ON t.event_id = e.id
JOIN event_tickets et ON t.event_ticket_id = et.id
WHERE t.purchased_by = ?
  AND t.status = 'valid'
ORDER BY e.event_date DESC;
```

### Event details with stats
```sql
SELECT e.*, 
       c.name as category_name,
       u.full_name as organizer_name,
       COUNT(DISTINCT t.id) as tickets_sold,
       AVG(cm.rating) as avg_rating,
       COUNT(DISTINCT cm.id) as comment_count
FROM events e
JOIN categories c ON e.category_id = c.id
JOIN users u ON e.created_by = u.id
LEFT JOIN tickets t ON e.id = t.event_id AND t.status != 'cancelled'
LEFT JOIN comments cm ON e.id = cm.event_id AND cm.status = 'visible'
WHERE e.slug = ?
GROUP BY e.id;
```

---

## 🚀 Next Steps

1. ✅ Create database
2. ✅ Run migration scripts
3. ✅ Seed sample data
4. ✅ Create API endpoints
5. ✅ Connect frontend to backend
6. ✅ Test all CRUD operations
7. ✅ Add authentication
8. ✅ Deploy to production

---

**Note**: Schema này có thể điều chỉnh tùy theo yêu cầu cụ thể của project.

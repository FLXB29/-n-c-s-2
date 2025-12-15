-- =====================================================
-- TEST DATA SEEDER - Events, Ticket Types, Orders, Tickets
-- Run this SQL in your MySQL/phpMyAdmin
-- =====================================================

-- =====================================================
-- THÊM CÁC SỰ KIỆN MỚI (EVENTS)
-- =====================================================

INSERT INTO `events` (`organizer_id`, `category_id`, `title`, `slug`, `description`, `short_description`, `featured_image`, `start_datetime`, `end_datetime`, `venue_name`, `venue_address`, `venue_city`, `is_free`, `total_tickets`, `tickets_sold`, `min_price`, `max_price`, `is_featured`, `allow_comments`, `status`, `visibility`, `view_count`, `like_count`, `created_at`, `updated_at`) VALUES

-- Sự kiện Âm nhạc
(3, 1, 'Rock Storm 2025 - Đại Nhạc Hội Rock', 'rock-storm-2025', 'Đại nhạc hội Rock lớn nhất Việt Nam quy tụ các ban nhạc rock hàng đầu: Bức Tường, Microwave, Ngũ Cung và nhiều nghệ sĩ khác. Một đêm nhạc rock đầy năng lượng và bùng nổ!', 'Đại nhạc hội Rock quy tụ các ban nhạc hàng đầu Việt Nam', 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800', '2025-01-15 19:00:00', '2025-01-15 23:00:00', 'Sân vận động Mỹ Đình', 'Đường Lê Đức Thọ, Nam Từ Liêm', 'Hà Nội', 0, 5000, 0, 200000, 1500000, 1, 1, 'published', 'public', 1520, 234, NOW() - INTERVAL 30 DAY, NOW()),

(6, 1, 'EDM Festival - Summer Vibes', 'edm-festival-summer-vibes', 'Lễ hội âm nhạc điện tử lớn nhất mùa hè với sự góp mặt của các DJ nổi tiếng quốc tế và trong nước. Âm thanh, ánh sáng đỉnh cao!', 'Lễ hội EDM với các DJ hàng đầu', 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800', '2025-01-20 18:00:00', '2025-01-21 02:00:00', 'Công viên Yên Sở', '77 Trần Duy Hưng, Cầu Giấy', 'Hà Nội', 0, 3000, 0, 350000, 2000000, 1, 1, 'published', 'public', 2100, 456, NOW() - INTERVAL 25 DAY, NOW()),

(8, 1, 'Acoustic Night - Những Bản Tình Ca', 'acoustic-night-tinh-ca', 'Đêm nhạc acoustic lãng mạn với những bản tình ca bất hủ. Không gian ấm cúng, âm nhạc nhẹ nhàng cùng các ca sĩ: Hà Anh Tuấn, Mỹ Tâm, Phan Mạnh Quỳnh.', 'Đêm nhạc acoustic lãng mạn với những bản tình ca bất hủ', 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800', '2025-01-25 20:00:00', '2025-01-25 23:00:00', 'Nhà hát Hòa Bình', '240 Đường 3 Tháng 2, Quận 10', 'Hồ Chí Minh', 0, 800, 0, 500000, 2500000, 1, 1, 'published', 'public', 890, 178, NOW() - INTERVAL 20 DAY, NOW()),

-- Sự kiện Thể thao
(3, 2, 'Vietnam Marathon 2025', 'vietnam-marathon-2025', 'Giải marathon quốc tế tại Việt Nam với các cự ly 5km, 10km, 21km và 42km. Chạy qua các cung đường đẹp nhất thành phố, nhận huy chương và quà tặng hấp dẫn!', 'Giải marathon quốc tế với nhiều cự ly', 'https://images.unsplash.com/photo-1513593771513-7b58b6c4af38?w=800', '2025-02-10 05:00:00', '2025-02-10 12:00:00', 'Phố đi bộ Nguyễn Huệ', 'Nguyễn Huệ, Quận 1', 'Hồ Chí Minh', 0, 10000, 0, 300000, 800000, 1, 1, 'published', 'public', 3500, 567, NOW() - INTERVAL 45 DAY, NOW()),

(6, 2, 'Yoga & Wellness Festival', 'yoga-wellness-festival', 'Lễ hội Yoga và sức khỏe lớn nhất năm với các buổi tập yoga ngoài trời, workshop thiền định, tư vấn dinh dưỡng và nhiều hoạt động wellness khác.', 'Lễ hội Yoga và sức khỏe với nhiều hoạt động', 'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800', '2025-02-15 06:00:00', '2025-02-15 18:00:00', 'Công viên Gia Định', '2 Hoàng Minh Giám, Phú Nhuận', 'Hồ Chí Minh', 0, 500, 0, 150000, 400000, 0, 1, 'published', 'public', 670, 89, NOW() - INTERVAL 35 DAY, NOW()),

-- Workshop
(8, 3, 'Workshop Nhiếp Ảnh - Từ Cơ Bản Đến Nâng Cao', 'workshop-nhiep-anh', 'Workshop nhiếp ảnh chuyên sâu với nhiếp ảnh gia chuyên nghiệp. Học cách sử dụng máy ảnh, bố cục, ánh sáng và hậu kỳ. Có thực hành outdoor.', 'Workshop nhiếp ảnh từ cơ bản đến nâng cao', 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800', '2025-01-28 09:00:00', '2025-01-28 17:00:00', 'Không gian sáng tạo Toong', '1 Bến Nghé, Quận 1', 'Hồ Chí Minh', 0, 30, 0, 800000, 1200000, 0, 1, 'published', 'public', 245, 45, NOW() - INTERVAL 15 DAY, NOW()),

(3, 3, 'Workshop Làm Bánh Pháp', 'workshop-lam-banh-phap', 'Học làm bánh Pháp với đầu bếp chuyên nghiệp. Croissant, Macaron, Éclair... Được thực hành và mang sản phẩm về nhà.', 'Workshop làm bánh Pháp với đầu bếp chuyên nghiệp', 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800', '2025-02-01 14:00:00', '2025-02-01 18:00:00', 'Bếp Bánh Saigon', '123 Nguyễn Thị Minh Khai, Quận 3', 'Hồ Chí Minh', 0, 20, 0, 600000, 600000, 0, 1, 'published', 'public', 180, 34, NOW() - INTERVAL 10 DAY, NOW()),

-- Hội thảo
(6, 4, 'Tech Summit Vietnam 2025', 'tech-summit-vietnam-2025', 'Hội nghị công nghệ lớn nhất Việt Nam quy tụ các chuyên gia từ Google, Microsoft, Facebook. Chủ đề: AI, Blockchain, Cloud Computing và Digital Transformation.', 'Hội nghị công nghệ với các chuyên gia hàng đầu', 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800', '2025-03-01 08:00:00', '2025-03-01 18:00:00', 'Trung tâm Hội nghị GEM Center', '8 Nguyễn Bỉnh Khiêm, Quận 1', 'Hồ Chí Minh', 0, 1000, 0, 1000000, 5000000, 1, 1, 'published', 'public', 2800, 345, NOW() - INTERVAL 60 DAY, NOW()),

(8, 4, 'Startup Pitching Night', 'startup-pitching-night', 'Đêm gọi vốn cho các startup Việt Nam. 10 startup trình bày ý tưởng trước các nhà đầu tư. Cơ hội networking và học hỏi kinh nghiệm.', 'Đêm gọi vốn cho startup', 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?w=800', '2025-02-20 18:00:00', '2025-02-20 22:00:00', 'Dreamplex Coworking', '195 Điện Biên Phủ, Bình Thạnh', 'Hồ Chí Minh', 0, 200, 0, 200000, 500000, 0, 1, 'published', 'public', 450, 67, NOW() - INTERVAL 40 DAY, NOW()),

-- Lễ hội
(3, 5, 'Tết Việt Festival 2025', 'tet-viet-festival-2025', 'Lễ hội Tết truyền thống với gian hàng ẩm thực, trò chơi dân gian, múa lân, viết thư pháp, gói bánh chưng và nhiều hoạt động văn hóa khác.', 'Lễ hội Tết truyền thống với nhiều hoạt động văn hóa', 'https://images.unsplash.com/photo-1518791841217-8f162f1e1131?w=800', '2025-01-26 09:00:00', '2025-01-28 21:00:00', 'Đầm Sen Park', '3 Hòa Bình, Quận 11', 'Hồ Chí Minh', 1, 0, 0, 0, 0, 1, 1, 'published', 'public', 5600, 890, NOW() - INTERVAL 50 DAY, NOW()),

(6, 5, 'Beer Festival Đà Nẵng', 'beer-festival-da-nang', 'Lễ hội bia quốc tế với hơn 50 loại bia từ khắp nơi trên thế giới. Biểu diễn âm nhạc, ẩm thực đường phố và nhiều hoạt động vui nhộn.', 'Lễ hội bia quốc tế với 50+ loại bia', 'https://images.unsplash.com/photo-1567696911980-2c669aad1fd4?w=800', '2025-03-15 16:00:00', '2025-03-16 23:00:00', 'Công viên Biển Đông', '1 Võ Nguyên Giáp, Sơn Trà', 'Đà Nẵng', 0, 2000, 0, 100000, 300000, 1, 1, 'published', 'public', 1890, 234, NOW() - INTERVAL 55 DAY, NOW()),

-- Nghệ thuật
(8, 6, 'Triển Lãm Nghệ Thuật Đương Đại', 'trien-lam-nghe-thuat-duong-dai', 'Triển lãm tranh và điêu khắc của các nghệ sĩ đương đại Việt Nam. Hơn 100 tác phẩm với đa dạng phong cách và chất liệu.', 'Triển lãm nghệ thuật đương đại Việt Nam', 'https://images.unsplash.com/photo-1531243269054-5ebf6f34081e?w=800', '2025-02-05 10:00:00', '2025-02-28 20:00:00', 'Bảo tàng Mỹ thuật TP.HCM', '97 Phó Đức Chính, Quận 1', 'Hồ Chí Minh', 0, 0, 0, 50000, 100000, 0, 1, 'published', 'public', 780, 123, NOW() - INTERVAL 28 DAY, NOW()),

(3, 6, 'Vở Kịch - Đêm Hè Không Trăng', 'vo-kich-dem-he-khong-trang', 'Vở kịch tâm lý xã hội đầy ám ảnh về số phận con người trong cuộc sống hiện đại. Diễn viên: NSƯT Thành Lộc, Hồng Vân.', 'Vở kịch tâm lý xã hội', 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?w=800', '2025-02-14 20:00:00', '2025-02-14 22:30:00', 'Nhà hát Kịch TP.HCM', '30 Trần Hưng Đạo, Quận 1', 'Hồ Chí Minh', 0, 400, 0, 200000, 800000, 1, 1, 'published', 'public', 560, 98, NOW() - INTERVAL 18 DAY, NOW()),

-- Thêm events cho các tháng trước để có data thống kê
(3, 1, 'Year End Party 2024', 'year-end-party-2024', 'Tiệc cuối năm hoành tráng với DJ, ca sĩ nổi tiếng và countdown chào năm mới 2025!', 'Tiệc cuối năm countdown 2025', 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800', '2024-12-31 20:00:00', '2025-01-01 02:00:00', 'Landmark 81 SkyView', 'Vinhomes Central Park, Bình Thạnh', 'Hồ Chí Minh', 0, 500, 320, 1000000, 3000000, 1, 1, 'published', 'public', 4500, 678, '2024-11-01 00:00:00', NOW()),

(6, 2, 'Giải Bóng Đá Mini Cup 2024', 'bong-da-mini-cup-2024', 'Giải bóng đá mini quy mô lớn dành cho các công ty và nhóm bạn bè. Giải thưởng hấp dẫn!', 'Giải bóng đá mini cho công ty', 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800', '2024-12-15 08:00:00', '2024-12-15 18:00:00', 'Sân bóng Phú Thọ', '1 Lữ Gia, Quận 11', 'Hồ Chí Minh', 0, 200, 180, 500000, 500000, 0, 1, 'published', 'public', 890, 123, '2024-11-01 00:00:00', NOW()),

(8, 4, 'Marketing Summit 2024', 'marketing-summit-2024', 'Hội thảo Marketing số với các chuyên gia hàng đầu. Xu hướng marketing 2025 và case study thực tế.', 'Hội thảo Marketing số', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800', '2024-12-10 09:00:00', '2024-12-10 17:00:00', 'Pullman Saigon Centre', '148 Trần Hưng Đạo, Quận 1', 'Hồ Chí Minh', 0, 300, 250, 800000, 2000000, 1, 1, 'published', 'public', 1200, 189, '2024-10-15 00:00:00', NOW());

-- =====================================================
-- LẤY ID CỦA CÁC EVENTS VỪA THÊM
-- =====================================================
SET @event_rock := (SELECT id FROM events WHERE slug = 'rock-storm-2025' LIMIT 1);
SET @event_edm := (SELECT id FROM events WHERE slug = 'edm-festival-summer-vibes' LIMIT 1);
SET @event_acoustic := (SELECT id FROM events WHERE slug = 'acoustic-night-tinh-ca' LIMIT 1);
SET @event_marathon := (SELECT id FROM events WHERE slug = 'vietnam-marathon-2025' LIMIT 1);
SET @event_yoga := (SELECT id FROM events WHERE slug = 'yoga-wellness-festival' LIMIT 1);
SET @event_photo := (SELECT id FROM events WHERE slug = 'workshop-nhiep-anh' LIMIT 1);
SET @event_banh := (SELECT id FROM events WHERE slug = 'workshop-lam-banh-phap' LIMIT 1);
SET @event_tech := (SELECT id FROM events WHERE slug = 'tech-summit-vietnam-2025' LIMIT 1);
SET @event_startup := (SELECT id FROM events WHERE slug = 'startup-pitching-night' LIMIT 1);
SET @event_beer := (SELECT id FROM events WHERE slug = 'beer-festival-da-nang' LIMIT 1);
SET @event_art := (SELECT id FROM events WHERE slug = 'trien-lam-nghe-thuat-duong-dai' LIMIT 1);
SET @event_kich := (SELECT id FROM events WHERE slug = 'vo-kich-dem-he-khong-trang' LIMIT 1);
SET @event_yearend := (SELECT id FROM events WHERE slug = 'year-end-party-2024' LIMIT 1);
SET @event_bongda := (SELECT id FROM events WHERE slug = 'bong-da-mini-cup-2024' LIMIT 1);
SET @event_marketing := (SELECT id FROM events WHERE slug = 'marketing-summit-2024' LIMIT 1);

-- =====================================================
-- THÊM TICKET TYPES CHO CÁC EVENTS
-- =====================================================

INSERT INTO `ticket_types` (`event_id`, `name`, `description`, `price`, `quantity`, `sold`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES

-- Rock Storm 2025
(@event_rock, 'Vé Phổ Thông', 'Khu vực đứng phổ thông', 200000, 3000, 450, 1, 1, NOW(), NOW()),
(@event_rock, 'Vé VIP', 'Khu vực VIP gần sân khấu', 800000, 1500, 280, 1, 2, NOW(), NOW()),
(@event_rock, 'Vé VVIP', 'Khu vực VVIP + meet & greet', 1500000, 500, 120, 1, 3, NOW(), NOW()),

-- EDM Festival
(@event_edm, 'Vé Early Bird', 'Vé mua sớm giá ưu đãi', 350000, 500, 500, 0, 1, NOW(), NOW()),
(@event_edm, 'Vé Thường', 'Vé phổ thông', 500000, 1500, 380, 1, 2, NOW(), NOW()),
(@event_edm, 'Vé VIP', 'Khu vực VIP + 2 đồ uống', 1200000, 800, 220, 1, 3, NOW(), NOW()),
(@event_edm, 'Vé Diamond', 'Khu vực riêng + all drinks', 2000000, 200, 80, 1, 4, NOW(), NOW()),

-- Acoustic Night
(@event_acoustic, 'Vé Thường', 'Ghế ngồi khu vực phổ thông', 500000, 400, 180, 1, 1, NOW(), NOW()),
(@event_acoustic, 'Vé VIP', 'Ghế VIP hàng đầu', 1500000, 300, 120, 1, 2, NOW(), NOW()),
(@event_acoustic, 'Vé Premium', 'Ghế Premium + gặp ca sĩ', 2500000, 100, 45, 1, 3, NOW(), NOW()),

-- Vietnam Marathon
(@event_marathon, 'Vé 5km', 'Cự ly 5km cho người mới', 300000, 3000, 890, 1, 1, NOW(), NOW()),
(@event_marathon, 'Vé 10km', 'Cự ly 10km', 400000, 3000, 670, 1, 2, NOW(), NOW()),
(@event_marathon, 'Vé 21km', 'Bán marathon 21km', 600000, 2500, 450, 1, 3, NOW(), NOW()),
(@event_marathon, 'Vé 42km', 'Full marathon 42km', 800000, 1500, 280, 1, 4, NOW(), NOW()),

-- Yoga Festival
(@event_yoga, 'Vé Ngày', 'Tham gia 1 ngày', 150000, 300, 120, 1, 1, NOW(), NOW()),
(@event_yoga, 'Vé Full Pass', 'Tham gia tất cả hoạt động + quà tặng', 400000, 200, 80, 1, 2, NOW(), NOW()),

-- Workshop Nhiếp ảnh
(@event_photo, 'Vé Học viên', 'Tham gia workshop full ngày', 800000, 20, 15, 1, 1, NOW(), NOW()),
(@event_photo, 'Vé VIP', 'Workshop + 1-1 với nhiếp ảnh gia', 1200000, 10, 8, 1, 2, NOW(), NOW()),

-- Workshop Bánh
(@event_banh, 'Vé Tham dự', 'Học làm bánh + nguyên liệu', 600000, 20, 18, 1, 1, NOW(), NOW()),

-- Tech Summit
(@event_tech, 'Vé Standard', 'Tham dự hội nghị', 1000000, 500, 280, 1, 1, NOW(), NOW()),
(@event_tech, 'Vé Business', 'Hội nghị + networking dinner', 3000000, 400, 180, 1, 2, NOW(), NOW()),
(@event_tech, 'Vé Premium', 'Full access + VIP lounge', 5000000, 100, 45, 1, 3, NOW(), NOW()),

-- Startup Pitching
(@event_startup, 'Vé Khán giả', 'Xem và networking', 200000, 150, 90, 1, 1, NOW(), NOW()),
(@event_startup, 'Vé Investor', 'Vé dành cho nhà đầu tư', 500000, 50, 35, 1, 2, NOW(), NOW()),

-- Beer Festival
(@event_beer, 'Vé Vào cửa', 'Vé vào cửa thường', 100000, 1500, 560, 1, 1, NOW(), NOW()),
(@event_beer, 'Vé Beer Pass', 'Vé + 5 ly bia miễn phí', 300000, 500, 280, 1, 2, NOW(), NOW()),

-- Triển lãm nghệ thuật
(@event_art, 'Vé Người lớn', 'Vé tham quan', 100000, 0, 180, 1, 1, NOW(), NOW()),
(@event_art, 'Vé Sinh viên', 'Vé sinh viên (cần HSSV)', 50000, 0, 95, 1, 2, NOW(), NOW()),

-- Vở kịch
(@event_kich, 'Vé Thường', 'Ghế ngồi tầng 2', 200000, 200, 120, 1, 1, NOW(), NOW()),
(@event_kich, 'Vé VIP', 'Ghế ngồi tầng 1', 500000, 150, 85, 1, 2, NOW(), NOW()),
(@event_kich, 'Vé Premium', 'Hàng ghế đầu + chụp ảnh', 800000, 50, 30, 1, 3, NOW(), NOW()),

-- Year End Party 2024
(@event_yearend, 'Vé Thường', 'Tham dự tiệc', 1000000, 300, 200, 1, 1, NOW(), NOW()),
(@event_yearend, 'Vé VIP', 'VIP table + champagne', 2000000, 150, 90, 1, 2, NOW(), NOW()),
(@event_yearend, 'Vé VVIP', 'VVIP private booth', 3000000, 50, 30, 1, 3, NOW(), NOW()),

-- Bóng đá Mini Cup
(@event_bongda, 'Vé Đội', 'Đăng ký 1 đội (10 người)', 500000, 20, 18, 1, 1, NOW(), NOW()),

-- Marketing Summit
(@event_marketing, 'Vé Standard', 'Tham dự hội thảo', 800000, 200, 150, 1, 1, NOW(), NOW()),
(@event_marketing, 'Vé VIP', 'Hội thảo + networking', 2000000, 100, 100, 1, 2, NOW(), NOW());

-- =====================================================
-- THÊM ORDERS ĐÃ THANH TOÁN (PAID)
-- =====================================================

-- Hàm tạo order_code ngẫu nhiên
-- Order cho Rock Storm
INSERT INTO `orders` (`order_code`, `user_id`, `event_id`, `total_amount`, `final_amount`, `status`, `payment_method`, `payment_status`, `created_at`, `updated_at`) VALUES
('ORD-RS001', 2, @event_rock, 200000, 200000, 'paid', 'banking', 'paid', NOW() - INTERVAL 20 DAY, NOW() - INTERVAL 20 DAY),
('ORD-RS002', 4, @event_rock, 800000, 800000, 'paid', 'banking', 'paid', NOW() - INTERVAL 18 DAY, NOW() - INTERVAL 18 DAY),
('ORD-RS003', 5, @event_rock, 1500000, 1500000, 'paid', 'momo', 'paid', NOW() - INTERVAL 15 DAY, NOW() - INTERVAL 15 DAY),
('ORD-RS004', 7, @event_rock, 400000, 400000, 'paid', 'banking', 'paid', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
('ORD-RS005', 10, @event_rock, 1600000, 1600000, 'paid', 'banking', 'paid', NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 10 DAY),
('ORD-RS006', 11, @event_rock, 3000000, 3000000, 'paid', 'momo', 'paid', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),

-- Order cho EDM Festival
('ORD-EDM001', 2, @event_edm, 500000, 500000, 'paid', 'banking', 'paid', NOW() - INTERVAL 22 DAY, NOW() - INTERVAL 22 DAY),
('ORD-EDM002', 4, @event_edm, 1200000, 1200000, 'paid', 'momo', 'paid', NOW() - INTERVAL 19 DAY, NOW() - INTERVAL 19 DAY),
('ORD-EDM003', 5, @event_edm, 2000000, 2000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 16 DAY, NOW() - INTERVAL 16 DAY),
('ORD-EDM004', 7, @event_edm, 1000000, 1000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 14 DAY, NOW() - INTERVAL 14 DAY),
('ORD-EDM005', 10, @event_edm, 500000, 500000, 'paid', 'momo', 'paid', NOW() - INTERVAL 11 DAY, NOW() - INTERVAL 11 DAY),

-- Order cho Acoustic Night
('ORD-AC001', 2, @event_acoustic, 1500000, 1500000, 'paid', 'banking', 'paid', NOW() - INTERVAL 17 DAY, NOW() - INTERVAL 17 DAY),
('ORD-AC002', 5, @event_acoustic, 2500000, 2500000, 'paid', 'banking', 'paid', NOW() - INTERVAL 14 DAY, NOW() - INTERVAL 14 DAY),
('ORD-AC003', 11, @event_acoustic, 500000, 500000, 'paid', 'momo', 'paid', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
('ORD-AC004', 14, @event_acoustic, 3000000, 3000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 10 DAY),

-- Order cho Marathon
('ORD-MR001', 2, @event_marathon, 300000, 300000, 'paid', 'banking', 'paid', NOW() - INTERVAL 40 DAY, NOW() - INTERVAL 40 DAY),
('ORD-MR002', 4, @event_marathon, 400000, 400000, 'paid', 'banking', 'paid', NOW() - INTERVAL 38 DAY, NOW() - INTERVAL 38 DAY),
('ORD-MR003', 5, @event_marathon, 600000, 600000, 'paid', 'momo', 'paid', NOW() - INTERVAL 35 DAY, NOW() - INTERVAL 35 DAY),
('ORD-MR004', 7, @event_marathon, 800000, 800000, 'paid', 'banking', 'paid', NOW() - INTERVAL 32 DAY, NOW() - INTERVAL 32 DAY),
('ORD-MR005', 10, @event_marathon, 300000, 300000, 'paid', 'banking', 'paid', NOW() - INTERVAL 30 DAY, NOW() - INTERVAL 30 DAY),
('ORD-MR006', 11, @event_marathon, 600000, 600000, 'paid', 'momo', 'paid', NOW() - INTERVAL 28 DAY, NOW() - INTERVAL 28 DAY),
('ORD-MR007', 14, @event_marathon, 400000, 400000, 'paid', 'banking', 'paid', NOW() - INTERVAL 25 DAY, NOW() - INTERVAL 25 DAY),

-- Order cho Yoga Festival
('ORD-YG001', 5, @event_yoga, 150000, 150000, 'paid', 'banking', 'paid', NOW() - INTERVAL 30 DAY, NOW() - INTERVAL 30 DAY),
('ORD-YG002', 11, @event_yoga, 400000, 400000, 'paid', 'momo', 'paid', NOW() - INTERVAL 28 DAY, NOW() - INTERVAL 28 DAY),

-- Order cho Workshop Nhiếp ảnh
('ORD-PH001', 2, @event_photo, 800000, 800000, 'paid', 'banking', 'paid', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
('ORD-PH002', 4, @event_photo, 1200000, 1200000, 'paid', 'banking', 'paid', NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 10 DAY),
('ORD-PH003', 7, @event_photo, 800000, 800000, 'paid', 'momo', 'paid', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),

-- Order cho Workshop Bánh
('ORD-BH001', 5, @event_banh, 600000, 600000, 'paid', 'banking', 'paid', NOW() - INTERVAL 8 DAY, NOW() - INTERVAL 8 DAY),
('ORD-BH002', 11, @event_banh, 600000, 600000, 'paid', 'momo', 'paid', NOW() - INTERVAL 6 DAY, NOW() - INTERVAL 6 DAY),

-- Order cho Tech Summit
('ORD-TS001', 2, @event_tech, 1000000, 1000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 55 DAY, NOW() - INTERVAL 55 DAY),
('ORD-TS002', 4, @event_tech, 3000000, 3000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 52 DAY, NOW() - INTERVAL 52 DAY),
('ORD-TS003', 7, @event_tech, 5000000, 5000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 50 DAY, NOW() - INTERVAL 50 DAY),
('ORD-TS004', 10, @event_tech, 1000000, 1000000, 'paid', 'momo', 'paid', NOW() - INTERVAL 48 DAY, NOW() - INTERVAL 48 DAY),
('ORD-TS005', 14, @event_tech, 3000000, 3000000, 'paid', 'banking', 'paid', NOW() - INTERVAL 45 DAY, NOW() - INTERVAL 45 DAY),

-- Order cho Startup Pitching
('ORD-SP001', 2, @event_startup, 200000, 200000, 'paid', 'banking', 'paid', NOW() - INTERVAL 35 DAY, NOW() - INTERVAL 35 DAY),
('ORD-SP002', 4, @event_startup, 500000, 500000, 'paid', 'banking', 'paid', NOW() - INTERVAL 32 DAY, NOW() - INTERVAL 32 DAY),

-- Order cho Beer Festival
('ORD-BF001', 2, @event_beer, 100000, 100000, 'paid', 'momo', 'paid', NOW() - INTERVAL 50 DAY, NOW() - INTERVAL 50 DAY),
('ORD-BF002', 4, @event_beer, 300000, 300000, 'paid', 'banking', 'paid', NOW() - INTERVAL 48 DAY, NOW() - INTERVAL 48 DAY),
('ORD-BF003', 7, @event_beer, 300000, 300000, 'paid', 'banking', 'paid', NOW() - INTERVAL 45 DAY, NOW() - INTERVAL 45 DAY),
('ORD-BF004', 10, @event_beer, 100000, 100000, 'paid', 'momo', 'paid', NOW() - INTERVAL 42 DAY, NOW() - INTERVAL 42 DAY),

-- Order cho Triển lãm nghệ thuật
('ORD-AR001', 5, @event_art, 100000, 100000, 'paid', 'banking', 'paid', NOW() - INTERVAL 25 DAY, NOW() - INTERVAL 25 DAY),
('ORD-AR002', 11, @event_art, 50000, 50000, 'paid', 'momo', 'paid', NOW() - INTERVAL 22 DAY, NOW() - INTERVAL 22 DAY),

-- Order cho Vở kịch
('ORD-KH001', 2, @event_kich, 500000, 500000, 'paid', 'banking', 'paid', NOW() - INTERVAL 15 DAY, NOW() - INTERVAL 15 DAY),
('ORD-KH002', 5, @event_kich, 800000, 800000, 'paid', 'banking', 'paid', NOW() - INTERVAL 12 DAY, NOW() - INTERVAL 12 DAY),
('ORD-KH003', 11, @event_kich, 200000, 200000, 'paid', 'momo', 'paid', NOW() - INTERVAL 10 DAY, NOW() - INTERVAL 10 DAY),

-- Order cho Year End Party (tháng 12/2024)
('ORD-YE001', 2, @event_yearend, 1000000, 1000000, 'paid', 'banking', 'paid', '2024-12-01 10:00:00', '2024-12-01 10:00:00'),
('ORD-YE002', 4, @event_yearend, 2000000, 2000000, 'paid', 'banking', 'paid', '2024-12-05 14:00:00', '2024-12-05 14:00:00'),
('ORD-YE003', 5, @event_yearend, 3000000, 3000000, 'paid', 'momo', 'paid', '2024-12-10 09:00:00', '2024-12-10 09:00:00'),
('ORD-YE004', 7, @event_yearend, 2000000, 2000000, 'paid', 'banking', 'paid', '2024-12-15 16:00:00', '2024-12-15 16:00:00'),
('ORD-YE005', 10, @event_yearend, 1000000, 1000000, 'paid', 'banking', 'paid', '2024-12-20 11:00:00', '2024-12-20 11:00:00'),
('ORD-YE006', 11, @event_yearend, 3000000, 3000000, 'paid', 'momo', 'paid', '2024-12-22 15:00:00', '2024-12-22 15:00:00'),
('ORD-YE007', 14, @event_yearend, 2000000, 2000000, 'paid', 'banking', 'paid', '2024-12-25 10:00:00', '2024-12-25 10:00:00'),

-- Order cho Bóng đá Mini Cup
('ORD-BD001', 2, @event_bongda, 500000, 500000, 'paid', 'banking', 'paid', '2024-11-20 09:00:00', '2024-11-20 09:00:00'),
('ORD-BD002', 4, @event_bongda, 500000, 500000, 'paid', 'banking', 'paid', '2024-11-22 10:00:00', '2024-11-22 10:00:00'),
('ORD-BD003', 7, @event_bongda, 500000, 500000, 'paid', 'momo', 'paid', '2024-11-25 14:00:00', '2024-11-25 14:00:00'),

-- Order cho Marketing Summit
('ORD-MK001', 2, @event_marketing, 800000, 800000, 'paid', 'banking', 'paid', '2024-11-01 09:00:00', '2024-11-01 09:00:00'),
('ORD-MK002', 4, @event_marketing, 2000000, 2000000, 'paid', 'banking', 'paid', '2024-11-05 10:00:00', '2024-11-05 10:00:00'),
('ORD-MK003', 5, @event_marketing, 800000, 800000, 'paid', 'momo', 'paid', '2024-11-10 14:00:00', '2024-11-10 14:00:00'),
('ORD-MK004', 10, @event_marketing, 2000000, 2000000, 'paid', 'banking', 'paid', '2024-11-15 11:00:00', '2024-11-15 11:00:00'),
('ORD-MK005', 14, @event_marketing, 800000, 800000, 'paid', 'banking', 'paid', '2024-11-20 16:00:00', '2024-11-20 16:00:00');

-- =====================================================
-- CẬP NHẬT TICKETS_SOLD CHO EVENTS
-- =====================================================
UPDATE events SET tickets_sold = (SELECT COUNT(*) FROM orders WHERE orders.event_id = events.id AND orders.status = 'paid') WHERE id >= @event_rock;

-- =====================================================
-- THÔNG BÁO HOÀN THÀNH
-- =====================================================
SELECT 'Data seeding completed!' AS message;
SELECT COUNT(*) AS 'New Events Added' FROM events WHERE id >= @event_rock;
SELECT COUNT(*) AS 'New Orders Added' FROM orders WHERE order_code LIKE 'ORD-%';

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting test data seeding...');

        // Lấy organizers
        $organizers = User::where('role', 'organizer')->pluck('id')->toArray();
        if (empty($organizers)) {
            $organizers = [3, 6, 8]; // fallback
        }

        // Lấy users để tạo orders
        $users = User::where('role', 'user')->pluck('id')->toArray();
        if (empty($users)) {
            $users = [2, 4, 5, 7, 10, 11, 14];
        }

        // Danh sách events mẫu
        $eventsData = [
            // Âm nhạc (category_id = 1)
            [
                'organizer_id' => $organizers[0] ?? 3,
                'category_id' => 1,
                'title' => 'Rock Storm 2025 - Đại Nhạc Hội Rock',
                'slug' => 'rock-storm-2025-' . Str::random(5),
                'description' => 'Đại nhạc hội Rock lớn nhất Việt Nam quy tụ các ban nhạc rock hàng đầu: Bức Tường, Microwave, Ngũ Cung và nhiều nghệ sĩ khác.',
                'short_description' => 'Đại nhạc hội Rock quy tụ các ban nhạc hàng đầu Việt Nam',
                'featured_image' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?w=800',
                'start_datetime' => Carbon::now()->addDays(30)->setTime(19, 0),
                'end_datetime' => Carbon::now()->addDays(30)->setTime(23, 0),
                'venue_name' => 'Sân vận động Mỹ Đình',
                'venue_address' => 'Đường Lê Đức Thọ, Nam Từ Liêm',
                'venue_city' => 'Hà Nội',
                'is_free' => false,
                'total_tickets' => 5000,
                'min_price' => 200000,
                'max_price' => 1500000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé Phổ Thông', 'price' => 200000, 'quantity' => 3000],
                    ['name' => 'Vé VIP', 'price' => 800000, 'quantity' => 1500],
                    ['name' => 'Vé VVIP', 'price' => 1500000, 'quantity' => 500],
                ]
            ],
            [
                'organizer_id' => $organizers[1] ?? 6,
                'category_id' => 1,
                'title' => 'EDM Festival - Summer Vibes',
                'slug' => 'edm-festival-summer-' . Str::random(5),
                'description' => 'Lễ hội âm nhạc điện tử lớn nhất mùa hè với sự góp mặt của các DJ nổi tiếng quốc tế và trong nước.',
                'short_description' => 'Lễ hội EDM với các DJ hàng đầu',
                'featured_image' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?w=800',
                'start_datetime' => Carbon::now()->addDays(45)->setTime(18, 0),
                'end_datetime' => Carbon::now()->addDays(46)->setTime(2, 0),
                'venue_name' => 'Công viên Yên Sở',
                'venue_address' => '77 Trần Duy Hưng, Cầu Giấy',
                'venue_city' => 'Hà Nội',
                'is_free' => false,
                'total_tickets' => 3000,
                'min_price' => 350000,
                'max_price' => 2000000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé Thường', 'price' => 500000, 'quantity' => 1500],
                    ['name' => 'Vé VIP', 'price' => 1200000, 'quantity' => 800],
                    ['name' => 'Vé Diamond', 'price' => 2000000, 'quantity' => 200],
                ]
            ],
            [
                'organizer_id' => $organizers[2] ?? 8,
                'category_id' => 1,
                'title' => 'Acoustic Night - Những Bản Tình Ca',
                'slug' => 'acoustic-night-' . Str::random(5),
                'description' => 'Đêm nhạc acoustic lãng mạn với những bản tình ca bất hủ.',
                'short_description' => 'Đêm nhạc acoustic lãng mạn',
                'featured_image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?w=800',
                'start_datetime' => Carbon::now()->addDays(20)->setTime(20, 0),
                'end_datetime' => Carbon::now()->addDays(20)->setTime(23, 0),
                'venue_name' => 'Nhà hát Hòa Bình',
                'venue_address' => '240 Đường 3 Tháng 2, Quận 10',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 800,
                'min_price' => 500000,
                'max_price' => 2500000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé Thường', 'price' => 500000, 'quantity' => 400],
                    ['name' => 'Vé VIP', 'price' => 1500000, 'quantity' => 300],
                    ['name' => 'Vé Premium', 'price' => 2500000, 'quantity' => 100],
                ]
            ],

            // Thể thao (category_id = 2)
            [
                'organizer_id' => $organizers[0] ?? 3,
                'category_id' => 2,
                'title' => 'Vietnam Marathon 2025',
                'slug' => 'vietnam-marathon-2025-' . Str::random(5),
                'description' => 'Giải marathon quốc tế tại Việt Nam với các cự ly 5km, 10km, 21km và 42km.',
                'short_description' => 'Giải marathon quốc tế với nhiều cự ly',
                'featured_image' => 'https://images.unsplash.com/photo-1513593771513-7b58b6c4af38?w=800',
                'start_datetime' => Carbon::now()->addDays(60)->setTime(5, 0),
                'end_datetime' => Carbon::now()->addDays(60)->setTime(12, 0),
                'venue_name' => 'Phố đi bộ Nguyễn Huệ',
                'venue_address' => 'Nguyễn Huệ, Quận 1',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 10000,
                'min_price' => 300000,
                'max_price' => 800000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé 5km', 'price' => 300000, 'quantity' => 3000],
                    ['name' => 'Vé 10km', 'price' => 400000, 'quantity' => 3000],
                    ['name' => 'Vé 21km', 'price' => 600000, 'quantity' => 2500],
                    ['name' => 'Vé 42km', 'price' => 800000, 'quantity' => 1500],
                ]
            ],

            // Workshop (category_id = 3)
            [
                'organizer_id' => $organizers[1] ?? 6,
                'category_id' => 3,
                'title' => 'Workshop Nhiếp Ảnh - Từ Cơ Bản Đến Nâng Cao',
                'slug' => 'workshop-nhiep-anh-' . Str::random(5),
                'description' => 'Workshop nhiếp ảnh chuyên sâu với nhiếp ảnh gia chuyên nghiệp.',
                'short_description' => 'Workshop nhiếp ảnh từ cơ bản đến nâng cao',
                'featured_image' => 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800',
                'start_datetime' => Carbon::now()->addDays(15)->setTime(9, 0),
                'end_datetime' => Carbon::now()->addDays(15)->setTime(17, 0),
                'venue_name' => 'Không gian sáng tạo Toong',
                'venue_address' => '1 Bến Nghé, Quận 1',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 30,
                'min_price' => 800000,
                'max_price' => 1200000,
                'is_featured' => false,
                'status' => 'published',
                'view_count' => rand(100, 500),
                'like_count' => rand(20, 100),
                'ticket_types' => [
                    ['name' => 'Vé Học viên', 'price' => 800000, 'quantity' => 20],
                    ['name' => 'Vé VIP 1-1', 'price' => 1200000, 'quantity' => 10],
                ]
            ],

            // Hội thảo (category_id = 4)
            [
                'organizer_id' => $organizers[2] ?? 8,
                'category_id' => 4,
                'title' => 'Tech Summit Vietnam 2025',
                'slug' => 'tech-summit-vietnam-2025-' . Str::random(5),
                'description' => 'Hội nghị công nghệ lớn nhất Việt Nam quy tụ các chuyên gia từ Google, Microsoft, Facebook.',
                'short_description' => 'Hội nghị công nghệ với các chuyên gia hàng đầu',
                'featured_image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800',
                'start_datetime' => Carbon::now()->addDays(75)->setTime(8, 0),
                'end_datetime' => Carbon::now()->addDays(75)->setTime(18, 0),
                'venue_name' => 'Trung tâm Hội nghị GEM Center',
                'venue_address' => '8 Nguyễn Bỉnh Khiêm, Quận 1',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 1000,
                'min_price' => 1000000,
                'max_price' => 5000000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé Standard', 'price' => 1000000, 'quantity' => 500],
                    ['name' => 'Vé Business', 'price' => 3000000, 'quantity' => 400],
                    ['name' => 'Vé Premium', 'price' => 5000000, 'quantity' => 100],
                ]
            ],

            // Lễ hội (category_id = 5)
            [
                'organizer_id' => $organizers[0] ?? 3,
                'category_id' => 5,
                'title' => 'Beer Festival Đà Nẵng 2025',
                'slug' => 'beer-festival-danang-' . Str::random(5),
                'description' => 'Lễ hội bia quốc tế với hơn 50 loại bia từ khắp nơi trên thế giới.',
                'short_description' => 'Lễ hội bia quốc tế với 50+ loại bia',
                'featured_image' => 'https://images.unsplash.com/photo-1567696911980-2c669aad1fd4?w=800',
                'start_datetime' => Carbon::now()->addDays(90)->setTime(16, 0),
                'end_datetime' => Carbon::now()->addDays(91)->setTime(23, 0),
                'venue_name' => 'Công viên Biển Đông',
                'venue_address' => '1 Võ Nguyên Giáp, Sơn Trà',
                'venue_city' => 'Đà Nẵng',
                'is_free' => false,
                'total_tickets' => 2000,
                'min_price' => 100000,
                'max_price' => 300000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(500, 3000),
                'like_count' => rand(50, 500),
                'ticket_types' => [
                    ['name' => 'Vé Vào cửa', 'price' => 100000, 'quantity' => 1500],
                    ['name' => 'Vé Beer Pass', 'price' => 300000, 'quantity' => 500],
                ]
            ],

            // Nghệ thuật (category_id = 6)
            [
                'organizer_id' => $organizers[1] ?? 6,
                'category_id' => 6,
                'title' => 'Vở Kịch - Đêm Hè Không Trăng',
                'slug' => 'vo-kich-dem-he-' . Str::random(5),
                'description' => 'Vở kịch tâm lý xã hội đầy ám ảnh về số phận con người trong cuộc sống hiện đại.',
                'short_description' => 'Vở kịch tâm lý xã hội',
                'featured_image' => 'https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?w=800',
                'start_datetime' => Carbon::now()->addDays(25)->setTime(20, 0),
                'end_datetime' => Carbon::now()->addDays(25)->setTime(22, 30),
                'venue_name' => 'Nhà hát Kịch TP.HCM',
                'venue_address' => '30 Trần Hưng Đạo, Quận 1',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 400,
                'min_price' => 200000,
                'max_price' => 800000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(200, 1000),
                'like_count' => rand(30, 200),
                'ticket_types' => [
                    ['name' => 'Vé Thường', 'price' => 200000, 'quantity' => 200],
                    ['name' => 'Vé VIP', 'price' => 500000, 'quantity' => 150],
                    ['name' => 'Vé Premium', 'price' => 800000, 'quantity' => 50],
                ]
            ],

            // Events đã diễn ra (để có dữ liệu thống kê)
            [
                'organizer_id' => $organizers[2] ?? 8,
                'category_id' => 1,
                'title' => 'Year End Party 2024',
                'slug' => 'year-end-party-2024-' . Str::random(5),
                'description' => 'Tiệc cuối năm hoành tráng với DJ, ca sĩ nổi tiếng và countdown chào năm mới 2025!',
                'short_description' => 'Tiệc cuối năm countdown 2025',
                'featured_image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800',
                'start_datetime' => Carbon::create(2024, 12, 31, 20, 0),
                'end_datetime' => Carbon::create(2025, 1, 1, 2, 0),
                'venue_name' => 'Landmark 81 SkyView',
                'venue_address' => 'Vinhomes Central Park, Bình Thạnh',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 500,
                'min_price' => 1000000,
                'max_price' => 3000000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(2000, 5000),
                'like_count' => rand(300, 800),
                'created_at' => Carbon::create(2024, 11, 1),
                'ticket_types' => [
                    ['name' => 'Vé Thường', 'price' => 1000000, 'quantity' => 300],
                    ['name' => 'Vé VIP', 'price' => 2000000, 'quantity' => 150],
                    ['name' => 'Vé VVIP', 'price' => 3000000, 'quantity' => 50],
                ]
            ],
            [
                'organizer_id' => $organizers[0] ?? 3,
                'category_id' => 4,
                'title' => 'Marketing Summit 2024',
                'slug' => 'marketing-summit-2024-' . Str::random(5),
                'description' => 'Hội thảo Marketing số với các chuyên gia hàng đầu.',
                'short_description' => 'Hội thảo Marketing số',
                'featured_image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800',
                'start_datetime' => Carbon::create(2024, 12, 10, 9, 0),
                'end_datetime' => Carbon::create(2024, 12, 10, 17, 0),
                'venue_name' => 'Pullman Saigon Centre',
                'venue_address' => '148 Trần Hưng Đạo, Quận 1',
                'venue_city' => 'Hồ Chí Minh',
                'is_free' => false,
                'total_tickets' => 300,
                'min_price' => 800000,
                'max_price' => 2000000,
                'is_featured' => true,
                'status' => 'published',
                'view_count' => rand(1000, 2000),
                'like_count' => rand(100, 300),
                'created_at' => Carbon::create(2024, 10, 15),
                'ticket_types' => [
                    ['name' => 'Vé Standard', 'price' => 800000, 'quantity' => 200],
                    ['name' => 'Vé VIP', 'price' => 2000000, 'quantity' => 100],
                ]
            ],
        ];

        $createdEvents = [];

        foreach ($eventsData as $eventData) {
            $ticketTypes = $eventData['ticket_types'];
            unset($eventData['ticket_types']);

            // Tạo event
            $event = Event::create($eventData);
            $createdEvents[] = $event;

            $this->command->info("Created event: {$event->title}");

            // Tạo ticket types
            foreach ($ticketTypes as $ttData) {
                $ticketType = TicketType::create([
                    'event_id' => $event->id,
                    'name' => $ttData['name'],
                    'description' => $ttData['name'],
                    'price' => $ttData['price'],
                    'quantity' => $ttData['quantity'],
                    'sold' => 0,
                    'is_active' => true,
                    'sort_order' => 1,
                ]);
            }
        }

        $this->command->info('Creating orders and tickets...');

        // Tạo orders cho các events
        foreach ($createdEvents as $event) {
            $ticketTypes = TicketType::where('event_id', $event->id)->get();
            
            if ($ticketTypes->isEmpty()) continue;

            // Tạo 3-8 orders cho mỗi event
            $numOrders = rand(3, 8);
            
            for ($i = 0; $i < $numOrders; $i++) {
                $user_id = $users[array_rand($users)];
                $ticketType = $ticketTypes->random();
                $quantity = rand(1, 3);
                $totalAmount = $ticketType->price * $quantity;

                // Random ngày tạo order
                $daysAgo = rand(1, 60);
                $orderDate = Carbon::now()->subDays($daysAgo);

                // Tạo order
                $orderNumber = 'EH' . date('Ymd', strtotime($orderDate)) . rand(1000, 9999);
                $paymentMethods = ['bank_transfer', 'momo', 'zalopay', 'vnpay'];
                $order = Order::create([
                    'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                    'order_number' => $orderNumber,
                    'user_id' => $user_id,
                    'event_id' => $event->id,
                    'total_amount' => $totalAmount,
                    'final_amount' => $totalAmount,
                    'status' => 'paid',
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'payment_status' => 'paid',
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                // Tạo tickets cho order
                for ($j = 0; $j < $quantity; $j++) {
                    Ticket::create([
                        'ticket_code' => 'TKT-' . strtoupper(Str::random(10)),
                        'event_id' => $event->id,
                        'ticket_type_id' => $ticketType->id,
                        'user_id' => $user_id,
                        'order_id' => $order->id,
                        'price_paid' => $ticketType->price,
                        'status' => 'active', // ENUM: active, used, cancelled, refunded, pending
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);

                    // Cập nhật sold count
                    $ticketType->increment('sold');
                }

                // Cập nhật tickets_sold cho event
                $event->increment('tickets_sold', $quantity);
            }
        }

        $this->command->info('Test data seeding completed!');
        $this->command->info('Created ' . count($createdEvents) . ' events with ticket types, orders, and tickets.');
    }
}

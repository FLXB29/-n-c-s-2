<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    public function run()
    {
        // Xóa dữ liệu cũ nếu muốn chạy lại sạch sẽ (Optional)
        // Seat::truncate(); 

        $events = Event::with('ticketTypes')->get();

        foreach ($events as $event) {
            // Biến đếm để tạo hàng ghế liên tục (A, B, C...)
            $currentRowIndex = 0;

            foreach ($event->ticketTypes as $type) {
                // Tạo tên Section
                $sectionName = strtoupper($type->name); 
                
                // Giả sử mỗi loại vé có 3 hàng ghế
                $rowsPerType = 3;

                for ($i = 0; $i < $rowsPerType; $i++) {
                    // Tạo tên hàng: A, B, C... dựa trên biến đếm tổng
                    $rowLabel = chr(65 + $currentRowIndex); 
                    $currentRowIndex++; // Tăng biến đếm lên cho hàng tiếp theo

                    // Mỗi hàng 10 ghế
                    for ($seatNum = 1; $seatNum <= 10; $seatNum++) {
                        
                        // Random trạng thái
                        $rand = rand(1, 100);
                        $status = 'available';
                        if ($rand > 95) $status = 'sold';      // 5% đã bán
                        elseif ($rand > 90) $status = 'reserved'; // 5% đang giữ

                        Seat::create([
                            'event_id' => $event->id,
                            'ticket_type_id' => $type->id,
                            'section' => $sectionName,
                            'row_number' => $rowLabel, // Lưu A, B, C...
                            'seat_number' => (string)$seatNum, // Lưu 1, 2, 3...
                            'status' => $status
                        ]);
                    }
                }
            }
        }
    }
}
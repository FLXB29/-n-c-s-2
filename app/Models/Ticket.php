<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',    // Mã vé (Unique string, dùng để quét QR)
        'event_id',
        'ticket_type_id',
        'seat_id',        // Ghế đã chọn (nếu có)
        'user_id',        // Người mua
        'order_id',       // Thuộc đơn hàng nào (nếu có model Order)
        'price_paid',     // Giá tiền thực tế đã trả (phòng khi giá vé thay đổi sau này)
        'status',         // 'paid', 'used', 'cancelled'
        'check_in_time'   // Thời gian vào cổng
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'price_paid' => 'decimal:0',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
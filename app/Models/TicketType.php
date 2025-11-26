<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',         // Tên loại vé (VIP, Thường...)
        'description',  // Mô tả quyền lợi
        'price',        // Giá tiền
        'quantity',     // Tổng số lượng vé phát hành
        'sold',         // Số lượng đã bán
        'is_active',    // Đang mở bán hay không
        'sort_order'    // Thứ tự hiển thị
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:0', // Hoặc decimal:2 tùy database
    ];

    // Quan hệ ngược về Sự kiện
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Quan hệ với các vé đã bán
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Hàm kiểm tra xem còn vé không
    public function isAvailable()
    {
        return $this->is_active && ($this->quantity > $this->sold);
    }
    
    // Hàm lấy số vé còn lại
    public function getRemainingAttribute()
    {
        return max(0, $this->quantity - $this->sold);
    }
}
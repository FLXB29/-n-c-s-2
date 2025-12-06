<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    // Các trạng thái ghế
    const STATUS_AVAILABLE = 'available';
    const STATUS_HELD = 'held';
    const STATUS_SOLD = 'sold';

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'section',
        'row_number',
        'seat_number',
        'status',
        'held_by_user_id',
        'reserved_until'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function heldByUser()
    {
        return $this->belongsTo(User::class, 'held_by_user_id');
    }

    /**
     * Kiểm tra ghế có đang available không (bao gồm cả ghế held đã hết hạn)
     */
    public function isAvailable(): bool
    {
        if ($this->status === self::STATUS_AVAILABLE) {
            return true;
        }

        // Nếu đang held nhưng đã hết hạn => coi như available
        if ($this->status === self::STATUS_HELD && $this->reserved_until && now()->greaterThan($this->reserved_until)) {
            return true;
        }

        return false;
    }

    /**
     * Kiểm tra ghế có đang bị hold bởi user cụ thể không
     */
    public function isHeldBy($userId): bool
    {
        return $this->status === self::STATUS_HELD
            && $this->held_by_user_id == $userId
            && $this->reserved_until
            && now()->lessThan($this->reserved_until);
    }

    /**
     * Scope: lấy ghế available (bao gồm cả held đã hết hạn)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_AVAILABLE)
                ->orWhere(function ($q2) {
                    $q2->where('status', self::STATUS_HELD)
                        ->where('reserved_until', '<', now());
                });
        });
    }

    // $query->where('status',self::STATUS_AVAILABLE)
    //         ->orwhere('status',self::STATUS_HELD)
    //         ->where('reserved_until','<',now());
    //là sai vì orwhere đã phá hỏng cấu trúc do mức độ ưu tiên của and và or thì and cao hơn
    //do đó nó sẽ ưu tiên truy vấn and ở dưới hơn tức là nếu availble thì còn phải có thêm cả
    //reserved < now nữa thì mới nhận
}

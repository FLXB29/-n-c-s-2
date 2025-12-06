<?php

namespace App\Events;

use App\Models\Seat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $seatId;
    public $eventId;
    public $status;
    public $heldByUserId;
    public $expiresAt;
    public $section;
    public $rowNumber;
    public $seatNumber;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Seat $seat)
    {
        $this->seatId = $seat->id;
        $this->eventId = $seat->event_id;
        $this->status = $seat->status;
        $this->heldByUserId = $seat->held_by_user_id;
        $this->expiresAt = $seat->reserved_until;
        $this->section = $seat->section;
        $this->rowNumber = $seat->row_number;
        $this->seatNumber = $seat->seat_number;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()//xác định kênh lắng nghe
    {
        // Broadcast trên channel public theo event_id
        return new Channel('event.' . $this->eventId . '.seats');
    }

    /**
     * Tên event khi broadcast để cho js xử lý 
     */
    public function broadcastAs()
    {
        return 'seat.status.changed';
    }

    /**
     * Dữ liệu gửi đi
     */
    public function broadcastWith()
    {
        return [
            'seat_id' => $this->seatId,
            'event_id' => $this->eventId,
            'status' => $this->status,
            'held_by_user_id' => $this->heldByUserId,
            'expires_at' => $this->expiresAt,
            'section' => $this->section,
            'row_number' => $this->rowNumber,
            'seat_number' => $this->seatNumber,
        ];
    }
}

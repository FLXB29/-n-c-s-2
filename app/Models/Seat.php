<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 
        'ticket_type_id', 
        'section', 
        'row_number', 
        'seat_number', 
        'status', 
        'reserved_until'
    ];

    protected $casts = [
        'reserved_until' => 'datetime',
    ];

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }
}
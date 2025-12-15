<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Hiển thị danh sách vé của user
     */
    public function myTickets()
    {
        $tickets = Ticket::with(['event', 'ticketType', 'seat', 'order'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['active', 'used'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Nhóm vé theo event
        $ticketsByEvent = $tickets->groupBy('event_id');

        return view('tickets.my-tickets', compact('tickets', 'ticketsByEvent'));
    }

    /**
     * Hiển thị chi tiết vé với QR code
     */
    public function show(Ticket $ticket)
    {
        // Chỉ cho phép xem vé của chính mình
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem vé này.');
        }

        $ticket->load(['event', 'ticketType', 'seat', 'order']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * API lấy thông tin vé (cho AJAX)
     */
    public function getTicketInfo(Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $ticket->load(['event', 'ticketType', 'seat']);

        return response()->json([
            'ticket_code' => $ticket->ticket_code,
            'event_name' => $ticket->event->title,
            'ticket_type' => $ticket->ticketType->name ?? 'N/A',
            'seat' => $ticket->seat ? $ticket->seat->seat_code : 'Không chọn ghế',
            'status' => $ticket->status,
            'price_paid' => number_format($ticket->price_paid, 0, ',', '.') . ' đ',
            'event_date' => $ticket->event->start_datetime->format('d/m/Y H:i'),
            'venue' => $ticket->event->venue_name,
            'check_in_time' => $ticket->check_in_time ? $ticket->check_in_time->format('d/m/Y H:i:s') : null,
        ]);
    }
}

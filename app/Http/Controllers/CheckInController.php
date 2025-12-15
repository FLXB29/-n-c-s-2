<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckInController extends Controller
{
    /**
     * Hiển thị trang quét QR cho admin/organizer
     */
    public function index()
    {
        $user = Auth::user();

        // Admin có thể check-in tất cả events
        // Organizer chỉ có thể check-in events của mình
        if ($user->role === 'admin') {
            $events = Event::where('status', 'published')
                ->where('start_datetime', '<=', now()->addDay())
                ->orderBy('start_datetime', 'desc')
                ->get();
        } else {
            $events = Event::where('organizer_id', $user->id)
                ->where('status', 'published')
                ->orderBy('start_datetime', 'desc')
                ->get();
        }

        return view('admin.check-in.index', compact('events'));
    }

    /**
     * Quét và kiểm tra vé
     */
    public function scan(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $ticketCode = $request->ticket_code;
        $eventId = $request->event_id;

        // Tìm vé
        $query = Ticket::with(['event', 'ticketType', 'seat', 'user'])
            ->where('ticket_code', $ticketCode);

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $ticket = $query->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vé với mã này!',
                'type' => 'error'
            ], 404);
        }

        // Kiểm tra quyền (organizer chỉ check-in event của mình)
        $user = Auth::user();
        if ($user->role !== 'admin' && $ticket->event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền check-in vé này!',
                'type' => 'error'
            ], 403);
        }

        // Kiểm tra trạng thái vé
        if ($ticket->status === 'used') {
            return response()->json([
                'success' => false,
                'message' => 'Vé đã được sử dụng!',
                'type' => 'warning',
                'ticket' => $this->formatTicketData($ticket),
                'check_in_time' => $ticket->check_in_time->format('d/m/Y H:i:s')
            ], 400);
        }

        if ($ticket->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Vé đã bị hủy!',
                'type' => 'error',
                'ticket' => $this->formatTicketData($ticket)
            ], 400);
        }

        if ($ticket->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Vé không hợp lệ! Trạng thái: ' . $ticket->status,
                'type' => 'error',
                'ticket' => $this->formatTicketData($ticket)
            ], 400);
        }

        // Kiểm tra sự kiện đã diễn ra chưa
        $eventDate = $ticket->event->start_datetime;
        $now = Carbon::now();

        // Cho phép check-in trước 2 giờ và sau 4 giờ kể từ giờ bắt đầu
        $checkInStart = $eventDate->copy()->subHours(2);
        $checkInEnd = $eventDate->copy()->addHours(4);

        if ($now->lt($checkInStart)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đến giờ check-in! Có thể check-in từ: ' . $checkInStart->format('d/m/Y H:i'),
                'type' => 'warning',
                'ticket' => $this->formatTicketData($ticket)
            ], 400);
        }

        // Trả về thông tin để xác nhận check-in (chưa check-in ngay)
        return response()->json([
            'success' => true,
            'message' => 'Vé hợp lệ! Xác nhận check-in?',
            'type' => 'success',
            'ticket' => $this->formatTicketData($ticket),
            'can_check_in' => true
        ]);
    }

    /**
     * Xác nhận check-in
     */
    public function confirmCheckIn(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $ticket = Ticket::with(['event', 'ticketType', 'seat', 'user'])
            ->where('ticket_code', $request->ticket_code)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy vé!',
            ], 404);
        }

        // Kiểm tra quyền
        $user = Auth::user();
        if ($user->role !== 'admin' && $ticket->event->organizer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền check-in vé này!',
            ], 403);
        }

        if ($ticket->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Vé không ở trạng thái có thể check-in!',
            ], 400);
        }

        // Thực hiện check-in
        $ticket->status = 'used';
        $ticket->check_in_time = Carbon::now();
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'ticket' => $this->formatTicketData($ticket),
            'check_in_time' => $ticket->check_in_time->format('d/m/Y H:i:s')
        ]);
    }

    /**
     * Lấy danh sách vé đã check-in của một sự kiện
     */
    public function getCheckedInList(Event $event)
    {
        $user = Auth::user();

        // Kiểm tra quyền
        if ($user->role !== 'admin' && $event->organizer_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tickets = Ticket::with(['user', 'ticketType', 'seat'])
            ->where('event_id', $event->id)
            ->where('status', 'used')
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($ticket) {
                $seatDisplay = 'Không có ghế';
                if ($ticket->seat) {
                    $seatDisplay = $ticket->seat->section . ' - ' . $ticket->seat->row_number . $ticket->seat->seat_number;
                }
                return [
                    'ticket_code' => $ticket->ticket_code,
                    'user_name' => $ticket->user->name ?? 'N/A',
                    'ticket_type' => $ticket->ticketType->name ?? 'N/A',
                    'seat' => $seatDisplay,
                    'check_in_time' => $ticket->check_in_time->format('H:i:s'),
                ];
            });

        $stats = [
            'total' => Ticket::where('event_id', $event->id)->whereIn('status', ['active', 'used'])->count(),
            'checked_in' => Ticket::where('event_id', $event->id)->where('status', 'used')->count(),
        ];

        return response()->json([
            'tickets' => $tickets,
            'stats' => $stats
        ]);
    }

    /**
     * Format ticket data for response
     */
    private function formatTicketData($ticket)
    {
        // Format seat display
        $seatDisplay = 'Không có ghế';
        if ($ticket->seat) {
            $seatDisplay = $ticket->seat->section . ' - Hàng ' . $ticket->seat->row_number . ' - Số ' . $ticket->seat->seat_number;
        }

        return [
            'ticket_code' => $ticket->ticket_code,
            'event_name' => $ticket->event->title,
            'event_date' => $ticket->event->start_datetime->format('d/m/Y H:i'),
            'venue' => $ticket->event->venue_name,
            'ticket_type' => $ticket->ticketType->name ?? 'N/A',
            'seat' => $seatDisplay,
            'user_name' => $ticket->user->name ?? 'N/A',
            'user_email' => $ticket->user->email ?? 'N/A',
            'price_paid' => number_format($ticket->price_paid, 0, ',', '.') . ' đ',
            'status' => $ticket->status,
            'check_in_time' => $ticket->check_in_time ? $ticket->check_in_time->format('d/m/Y H:i:s') : null,
        ];
    }
}

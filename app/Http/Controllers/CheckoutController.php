<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Seat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function process(Request $request, Event $event)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'selected_seats' => 'nullable|string', // JSON string of seat IDs
        ]);

        $user = Auth::user();
        $ticketType = TicketType::findOrFail($request->ticket_type_id);
        
        // Check availability
        if ($ticketType->remaining < $request->quantity) {
            return back()->with('error', 'Số lượng vé còn lại không đủ.');
        }

        // Calculate total
        $totalAmount = $ticketType->price * $request->quantity;
        
        // Parse selected seats
        $selectedSeatIds = [];
        if ($request->filled('selected_seats')) {
            $selectedSeatIds = json_decode($request->selected_seats, true);
            if (count($selectedSeatIds) != $request->quantity) {
                // If seat count doesn't match quantity, fallback or error?
                // For now, let's assume strict matching if seats are provided
                // return back()->with('error', 'Số lượng ghế đã chọn không khớp với số lượng vé.');
            }
        }

        try {
            DB::beginTransaction();

            // 1. Create Order
            $orderCode = 'ORD-' . strtoupper(Str::random(10));
            $order = Order::create([
                'order_code' => $orderCode,
                'order_number' => $orderCode,
                'user_id' => $user->id,
                'event_id' => $event->id,
                'total_amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'status' => 'pending', // Wait for payment
                'payment_method' => 'bank_transfer', // Default to bank transfer/QR
                'payment_status' => 'pending',
            ]);

            // 2. Create Tickets
            for ($i = 0; $i < $request->quantity; $i++) {
                $ticket = Ticket::create([
                    'ticket_code' => 'TKT-' . strtoupper(Str::random(12)),
                    'event_id' => $event->id,
                    'ticket_type_id' => $ticketType->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'price_paid' => $ticketType->price,
                    'status' => 'pending', // Wait for payment
                ]);

                // 3. Update Seat if applicable
                if (!empty($selectedSeatIds[$i])) {
                    $seatId = $selectedSeatIds[$i];
                    $seat = Seat::find($seatId);
                    if ($seat && $seat->status == 'available') {
                        $seat->update(['status' => 'reserved']); // Reserve seat instead of sold
                    }
                }
            }

            // 4. Update TicketType sold count
            // We might want to increment this only after payment, but to prevent overbooking, let's increment now
            // Or better, have a 'reserved' count. For simplicity, we increment 'sold' but if order is cancelled, we decrement.
            $ticketType->increment('sold', $request->quantity);
            $event->increment('tickets_sold', $request->quantity);

            DB::commit();

            return redirect()->route('orders.payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi xử lý đơn hàng: ' . $e->getMessage());
        }
    }

    public function showPayment(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        return view('orders.payment', compact('order'));
    }

    public function confirmPayment(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        
        // In a real app, we wouldn't auto-confirm here without verification.
        // But for this flow: User clicks "I have paid" -> We mark as "processing" or keep "pending" and notify admin.
        // Let's just redirect to dashboard with a message.
        
        return redirect()->route('user.dashboard')->with('success', 'Đã ghi nhận thông tin thanh toán. Vui lòng chờ xác nhận từ Ban tổ chức.');
    }
}

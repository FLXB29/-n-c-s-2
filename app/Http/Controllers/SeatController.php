<?php

namespace App\Http\Controllers;

use App\Models\Seat;
use App\Models\Event;
use App\Events\SeatStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SeatController extends Controller
{
    /**
     * Thời gian giữ ghế (phút)
     */
    const HOLD_MINUTES = 5;

    /**
     * Lấy danh sách ghế của sự kiện
     */
    public function index($eventId)
    {
        $seats = Seat::where('event_id', $eventId)
            ->select('id', 'section', 'row_number', 'seat_number', 'status', 'held_by_user_id', 'reserved_until', 'ticket_type_id')
            ->get()
            ->map(function ($seat) {
                // Auto-release ghế held đã hết hạn
                if ($seat->status === Seat::STATUS_HELD && $seat->reserved_until && now()->greaterThan($seat->reserved_until)) {
                    $seat->status = Seat::STATUS_AVAILABLE;
                    $seat->held_by_user_id = null;
                }
                return $seat;
            });

        return response()->json([
            'success' => true,
            'seats' => $seats,
            'current_user_id' => auth()->id(),
        ]);
    }

    /**
     * Giữ ghế tạm thời (hold)
     */
    public function hold(Request $request, $eventId)
    {
        $request->validate([
            'seat_id' => 'required|integer',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để chọn ghế.',
            ], 401);
        }

        try {
            //đảm bảo toàn bộ khối lệnh trong này đều phải được thực hiện thành công nếu
            //có một cái thất bại sẽ bị rollback và hủy toàn bộ quá trình
            $result = DB::transaction(function () use ($request, $eventId, $userId) {
                // Lock row để tránh race condition
                $seat = Seat::where('id', $request->seat_id)
                    ->where('event_id', $eventId)
                    ->lockForUpdate()//lock ghế này trong database để bất kì tiến trình khác
                                     //truy cập vào thì phải chờ
                    ->firstOrFail();

                // Kiểm tra nếu ghế held đã hết hạn => auto release
                if ($seat->status === Seat::STATUS_HELD && $seat->reserved_until && now()->greaterThan($seat->reserved_until)) {
                    $seat->status = Seat::STATUS_AVAILABLE;
                    $seat->held_by_user_id = null;
                    $seat->reserved_until = null;
                }

                // Kiểm tra ghế có available không
                if ($seat->status !== Seat::STATUS_AVAILABLE) {
                    return [
                        'success' => false,
                        'message' => 'Ghế này đã được chọn bởi người khác.',
                        'seat' => $seat,
                    ];
                }

                // Hold ghế
                $seat->status = Seat::STATUS_HELD;
                $seat->held_by_user_id = $userId;
                $seat->reserved_until = Carbon::now()->addMinutes(self::HOLD_MINUTES);
                $seat->save();

                // Broadcast sự kiện
                broadcast(new SeatStatusChanged($seat))->toOthers();

                return [
                    'success' => true,
                    'message' => 'Đã giữ ghế thành công.',
                    'seat' => $seat,
                    'expires_at' => $seat->reserved_until->toISOString(),
                ];
            });

            return response()->json($result, $result['success'] ? 200 : 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Thả ghế đang giữ (release)
     */
    public function release(Request $request, $eventId)
    {
        $request->validate([
            'seat_id' => 'required|integer',
        ]);

        $userId = auth()->id();

        try {
            $result = DB::transaction(function () use ($request, $eventId, $userId) {
                $seat = Seat::where('id', $request->seat_id)
                    ->where('event_id', $eventId)
                    ->lockForUpdate()//đây là khóa lại không cho truy cập mà phải chờ
                    ->firstOrFail();

                // Chỉ release nếu đang được hold bởi chính user này
                if ($seat->status === Seat::STATUS_HELD && $seat->held_by_user_id == $userId) {
                    $seat->status = Seat::STATUS_AVAILABLE;
                    $seat->held_by_user_id = null;
                    $seat->reserved_until = null;
                    $seat->save();

                    // Broadcast sự kiện
                    broadcast(new SeatStatusChanged($seat))->toOthers();

                    return [
                        'success' => true,
                        'message' => 'Đã thả ghế thành công.',
                        'seat' => $seat,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Không thể thả ghế này.',
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Giữ nhiều ghế cùng lúc
     */
    public function holdMultiple(Request $request, $eventId)
    {
        $request->validate([
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'integer',
        ]);

        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để chọn ghế.',
            ], 401);
        }

        try {
            $result = DB::transaction(function () use ($request, $eventId, $userId) {
                $seatIds = $request->seat_ids;
                $heldSeats = [];
                $failedSeats = [];

                foreach ($seatIds as $seatId) {
                    $seat = Seat::where('id', $seatId)
                        ->where('event_id', $eventId)
                        ->lockForUpdate()
                        ->first();

                    if (!$seat) {
                        $failedSeats[] = $seatId;
                        continue;
                    }

                    // Auto-release nếu hết hạn
                    if ($seat->status === Seat::STATUS_HELD && $seat->reserved_until && now()->greaterThan($seat->reserved_until)) {
                        $seat->status = Seat::STATUS_AVAILABLE;
                        $seat->held_by_user_id = null;
                        $seat->reserved_until = null;
                    }

                    if ($seat->status === Seat::STATUS_AVAILABLE) {
                        $seat->status = Seat::STATUS_HELD;
                        $seat->held_by_user_id = $userId;
                        $seat->reserved_until = Carbon::now()->addMinutes(self::HOLD_MINUTES);
                        $seat->save();

                        broadcast(new SeatStatusChanged($seat))->toOthers();
                        $heldSeats[] = $seat;
                    } else {
                        $failedSeats[] = $seatId;
                    }
                }

                return [
                    'success' => count($failedSeats) === 0,
                    'message' => count($failedSeats) === 0
                        ? 'Đã giữ tất cả ghế thành công.'
                        : 'Một số ghế không thể giữ được.',
                    'held_seats' => $heldSeats,
                    'failed_seat_ids' => $failedSeats,
                    'expires_at' => Carbon::now()->addMinutes(self::HOLD_MINUTES)->toISOString(),
                ];
            });

            return response()->json($result, $result['success'] ? 200 : 409);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Thả tất cả ghế đang giữ của user
     */
    public function releaseAll(Request $request, $eventId)
    {
        $userId = auth()->id();

        try {
            $seats = Seat::where('event_id', $eventId)
                ->where('held_by_user_id', $userId)
                ->where('status', Seat::STATUS_HELD)
                ->get();

            foreach ($seats as $seat) {
                $seat->status = Seat::STATUS_AVAILABLE;
                $seat->held_by_user_id = null;
                $seat->reserved_until = null;
                $seat->save();

                broadcast(new SeatStatusChanged($seat))->toOthers();
            }

            return response()->json([
                'success' => true,
                'message' => 'Đã thả tất cả ghế.',
                'released_count' => $seats->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}

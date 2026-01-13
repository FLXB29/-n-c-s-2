<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminStatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(60)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // Overview stats
        $stats = [
            'total_users' => User::count(),
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_events' => Event::count(),
            'new_events' => Event::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_orders' => Order::count(),
            'orders_in_range' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'paid_orders' => Order::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('final_amount'),
            'tickets_sold' => Ticket::whereIn('status', ['paid', 'active', 'used'])->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Revenue by day (for chart)
        $revenueByDay = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(final_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top events by revenue
        $topEvents = Event::select('events.id', 'events.title')
            ->join('orders', 'orders.event_id', '=', 'events.id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('events.id, events.title, SUM(orders.final_amount) as revenue, COUNT(orders.id) as order_count')
            ->groupBy('events.id', 'events.title')
            ->orderByDesc('revenue')
            ->take(10)
            ->get();

        // Orders by status
        $ordersByStatus = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Users by role
        $usersByRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return view('admin.statistics.index', compact(
            'stats',
            'revenueByDay',
            'topEvents',
            'ordersByStatus',
            'usersByRole',
            'startDate',
            'endDate'
        ));
    }

    public function exportOrders(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $orders = Order::with(['user', 'event'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            // Header row
            fputcsv($file, ['Mã đơn', 'Ngày tạo', 'Khách hàng', 'Email', 'Sự kiện', 'Tổng tiền', 'Trạng thái', 'Phương thức TT']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->created_at->format('d/m/Y H:i'),
                    $order->user->full_name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    $order->event->title ?? 'N/A',
                    $order->final_amount,
                    $order->status,
                    $order->payment_method,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportUsers(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->get();

        $filename = 'users_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Họ tên', 'Email', 'SĐT', 'Vai trò', 'Trạng thái', 'Ngày đăng ký']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->full_name,
                    $user->email,
                    $user->phone,
                    $user->role,
                    $user->status,
                    $user->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportEvents(Request $request)
    {
        $events = Event::with('organizer')->orderBy('created_at', 'desc')->get();

        $filename = 'events_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($events) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['ID', 'Tiêu đề', 'Tổ chức bởi', 'Ngày bắt đầu', 'Địa điểm', 'Vé đã bán', 'Trạng thái', 'Ngày tạo']);

            foreach ($events as $event) {
                fputcsv($file, [
                    $event->id,
                    $event->title,
                    $event->organizer->full_name ?? 'N/A',
                    $event->start_datetime ? $event->start_datetime->format('d/m/Y H:i') : 'N/A',
                    $event->venue_name,
                    $event->tickets_sold,
                    $event->status,
                    $event->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRevenue(Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $revenueByDay = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(final_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $filename = 'revenue_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($revenueByDay) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, ['Ngày', 'Doanh thu (VNĐ)', 'Số đơn']);

            foreach ($revenueByDay as $row) {
                fputcsv($file, [
                    Carbon::parse($row->date)->format('d/m/Y'),
                    $row->revenue,
                    $row->orders,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Thống kê tổng hợp theo Events
     */
    public function eventStats(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'revenue');
        $sortOrder = $request->input('order', 'desc');

        // Subquery for order stats
        $orderStats = DB::table('orders')
            ->select('event_id')
            ->selectRaw('SUM(final_amount) as total_revenue, COUNT(*) as total_orders')
            ->where('status', 'paid')
            ->groupBy('event_id');

        // Subquery for ticket stats
        $ticketStats = DB::table('tickets')
            ->select('event_id')
            ->selectRaw('COUNT(*) as total_tickets_sold')
            ->whereIn('status', ['paid', 'active', 'used'])
            ->groupBy('event_id');

        $query = Event::select('events.*')
            ->leftJoinSub($orderStats, 'order_stats', function ($join) {
                $join->on('events.id', '=', 'order_stats.event_id');
            })
            ->leftJoinSub($ticketStats, 'ticket_stats', function ($join) {
                $join->on('events.id', '=', 'ticket_stats.event_id');
            })
            ->selectRaw('events.*, 
                COALESCE(order_stats.total_revenue, 0) as total_revenue, 
                COALESCE(order_stats.total_orders, 0) as total_orders,
                COALESCE(ticket_stats.total_tickets_sold, 0) as total_tickets_sold');

        if ($search) {
            $query->where('events.title', 'like', '%' . $search . '%');
        }

        // Sorting
        switch ($sortBy) {
            case 'revenue':
                $query->orderBy('total_revenue', $sortOrder);
                break;
            case 'orders':
                $query->orderBy('total_orders', $sortOrder);
                break;
            case 'tickets':
                $query->orderBy('total_tickets_sold', $sortOrder);
                break;
            case 'date':
                $query->orderBy('events.start_datetime', $sortOrder);
                break;
            default:
                $query->orderBy('total_revenue', 'desc');
        }

        $events = $query->paginate(15);

        // Tổng doanh thu tất cả events
        $totalRevenue = Order::where('status', 'paid')->sum('final_amount');
        $totalOrders = Order::where('status', 'paid')->count();
        $totalTickets = Ticket::whereIn('status', ['paid', 'active', 'used'])->count();

        return view('admin.statistics.events', compact('events', 'search', 'sortBy', 'sortOrder', 'totalRevenue', 'totalOrders', 'totalTickets'));
    }

    /**
     * Thống kê chi tiết 1 Event
     */
    public function eventDetail(Event $event, Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $event->created_at->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // Tổng quan event
        $overview = [
            'total_revenue' => Order::where('event_id', $event->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('final_amount'),
            'total_orders' => Order::where('event_id', $event->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_tickets' => Ticket::where('event_id', $event->id)
                ->whereIn('status', ['paid', 'active', 'used'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'avg_order_value' => Order::where('event_id', $event->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('final_amount') ?? 0,
            'tickets_used' => Ticket::where('event_id', $event->id)
                ->where('status', 'used')
                ->count(),
        ];

        // Doanh thu theo loại vé
        $ticketSoldStats = DB::table('tickets')
            ->select('ticket_type_id')
            ->selectRaw('COUNT(*) as tickets_sold_count, COALESCE(SUM(price_paid), 0) as revenue')
            ->whereIn('status', ['paid', 'active', 'used'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('ticket_type_id');

        $ticketTypeStats = TicketType::where('ticket_types.event_id', $event->id)
            ->leftJoinSub($ticketSoldStats, 'ticket_sold_stats', function ($join) {
                $join->on('ticket_types.id', '=', 'ticket_sold_stats.ticket_type_id');
            })
            ->select('ticket_types.*')
            ->selectRaw('COALESCE(ticket_sold_stats.tickets_sold_count, 0) as tickets_sold_count, COALESCE(ticket_sold_stats.revenue, 0) as revenue')
            ->get();

        // Vé bán chạy nhất
        $bestSellingTicketType = $ticketTypeStats->sortByDesc('tickets_sold_count')->first();

        // Doanh thu theo ngày
        $revenueByDay = Order::where('event_id', $event->id)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(final_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Vé bán theo ngày
        $ticketsByDay = Ticket::where('event_id', $event->id)
            ->whereIn('status', ['paid', 'active', 'used'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top khách hàng mua nhiều nhất
        $topBuyerStats = DB::table('orders')
            ->select('user_id')
            ->selectRaw('COUNT(*) as order_count, SUM(final_amount) as total_spent')
            ->where('event_id', $event->id)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(10);

        $topBuyers = User::select('users.*')
            ->joinSub($topBuyerStats, 'buyer_stats', function ($join) {
                $join->on('users.id', '=', 'buyer_stats.user_id');
            })
            ->selectRaw('users.*, buyer_stats.order_count, buyer_stats.total_spent')
            ->orderByDesc('buyer_stats.total_spent')
            ->get();

        // Đơn hàng gần đây
        $recentOrders = Order::where('event_id', $event->id)
            ->with(['user', 'tickets.ticketType'])
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('admin.statistics.event-detail', compact(
            'event',
            'overview',
            'ticketTypeStats',
            'bestSellingTicketType',
            'revenueByDay',
            'ticketsByDay',
            'topBuyers',
            'recentOrders',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Thống kê tổng hợp theo Users
     */
    public function userStats(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'spent');
        $sortOrder = $request->input('order', 'desc');

        // Subquery for order stats
        $orderStats = DB::table('orders')
            ->select('user_id')
            ->selectRaw('SUM(final_amount) as total_spent, COUNT(*) as total_orders')
            ->where('status', 'paid')
            ->groupBy('user_id');

        // Subquery for ticket stats
        $ticketStats = DB::table('tickets')
            ->select('user_id')
            ->selectRaw('COUNT(*) as total_tickets')
            ->whereIn('status', ['paid', 'active', 'used'])
            ->groupBy('user_id');

        $query = User::select('users.*')
            ->leftJoinSub($orderStats, 'order_stats', function ($join) {
                $join->on('users.id', '=', 'order_stats.user_id');
            })
            ->leftJoinSub($ticketStats, 'ticket_stats', function ($join) {
                $join->on('users.id', '=', 'ticket_stats.user_id');
            })
            ->selectRaw('users.*, 
                COALESCE(order_stats.total_spent, 0) as total_spent, 
                COALESCE(order_stats.total_orders, 0) as total_orders,
                COALESCE(ticket_stats.total_tickets, 0) as total_tickets')
            ->where('users.role', '!=', 'admin');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.full_name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        switch ($sortBy) {
            case 'spent':
                $query->orderBy('total_spent', $sortOrder);
                break;
            case 'orders':
                $query->orderBy('total_orders', $sortOrder);
                break;
            case 'tickets':
                $query->orderBy('total_tickets', $sortOrder);
                break;
            case 'date':
                $query->orderBy('users.created_at', $sortOrder);
                break;
            default:
                $query->orderBy('total_spent', 'desc');
        }

        $users = $query->paginate(15);

        // Tổng hợp
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $activeUsers = User::where('role', '!=', 'admin')
            ->whereHas('orders', fn($q) => $q->where('status', 'paid'))
            ->count();

        return view('admin.statistics.users', compact('users', 'search', 'sortBy', 'sortOrder', 'totalUsers', 'activeUsers'));
    }

    /**
     * Thống kê chi tiết 1 User
     */
    public function userDetail(User $user, Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $user->created_at->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        // Tổng quan user
        $overview = [
            'total_spent' => Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('final_amount'),
            'total_orders' => Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_tickets' => Ticket::where('user_id', $user->id)
                ->whereIn('status', ['paid', 'active', 'used'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'avg_order_value' => Order::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('final_amount') ?? 0,
            'tickets_used' => Ticket::where('user_id', $user->id)
                ->where('status', 'used')
                ->count(),
            'pending_orders' => Order::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        // Mua theo category (loại sự kiện hay mua)
        $categoryOrderStats = DB::table('orders')
            ->join('events', 'events.id', '=', 'orders.event_id')
            ->select('events.category_id')
            ->selectRaw('COUNT(orders.id) as order_count, COALESCE(SUM(orders.final_amount), 0) as total_spent')
            ->where('orders.user_id', $user->id)
            ->where('orders.status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('events.category_id');

        $categoryStats = Category::select('categories.*')
            ->joinSub($categoryOrderStats, 'cat_stats', function ($join) {
                $join->on('categories.id', '=', 'cat_stats.category_id');
            })
            ->selectRaw('categories.*, cat_stats.order_count, cat_stats.total_spent')
            ->orderByDesc('cat_stats.order_count')
            ->get();

        // Loại sự kiện hay mua nhất
        $favoriteCategory = $categoryStats->first();

        // Chi tiêu theo tháng
        $spendingByMonth = Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(final_amount) as spent, COUNT(*) as orders')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Sự kiện đã tham gia
        $eventOrderStats = DB::table('orders')
            ->select('event_id')
            ->selectRaw('SUM(final_amount) as amount_spent, COUNT(*) as order_count')
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('event_id');

        $attendedEvents = Event::select('events.*')
            ->joinSub($eventOrderStats, 'event_stats', function ($join) {
                $join->on('events.id', '=', 'event_stats.event_id');
            })
            ->selectRaw('events.*, event_stats.amount_spent, event_stats.order_count')
            ->orderByDesc('event_stats.amount_spent')
            ->get();

        // Loại vé hay mua
        $ticketTypeStats = TicketType::select('ticket_types.name')
            ->join('tickets', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->where('tickets.user_id', $user->id)
            ->whereIn('tickets.status', ['paid', 'active', 'used'])
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->selectRaw('ticket_types.name, COUNT(tickets.id) as count, SUM(tickets.price_paid) as total')
            ->groupBy('ticket_types.name')
            ->orderByDesc('count')
            ->get();

        // Đơn hàng gần đây
        $recentOrders = Order::where('user_id', $user->id)
            ->with(['event', 'tickets.ticketType'])
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('admin.statistics.user-detail', compact(
            'user',
            'overview',
            'categoryStats',
            'favoriteCategory',
            'spendingByMonth',
            'attendedEvents',
            'ticketTypeStats',
            'recentOrders',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export chi tiết event
     */
    public function exportEventDetail(Event $event, Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $event->created_at->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $tickets = Ticket::where('event_id', $event->id)
            ->whereIn('status', ['paid', 'active', 'used'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['user', 'ticketType', 'seat'])
            ->get();

        $filename = 'event_' . $event->id . '_tickets_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($tickets, $event) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Event info
            fputcsv($file, ['Sự kiện:', $event->title]);
            fputcsv($file, ['']);
            fputcsv($file, ['Mã vé', 'Loại vé', 'Khách hàng', 'Email', 'Ghế', 'Giá', 'Trạng thái', 'Ngày mua']);

            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->ticket_code,
                    $ticket->ticketType->name ?? 'N/A',
                    $ticket->user->full_name ?? 'N/A',
                    $ticket->user->email ?? 'N/A',
                    $ticket->seat->seat_label ?? 'N/A',
                    $ticket->price_paid,
                    $ticket->status,
                    $ticket->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export chi tiết user
     */
    public function exportUserDetail(User $user, Request $request)
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->start_date)->startOfDay() : $user->created_at->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->end_date)->endOfDay() : now()->endOfDay();

        $orders = Order::where('user_id', $user->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['event', 'tickets.ticketType'])
            ->get();

        $filename = 'user_' . $user->id . '_orders_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders, $user) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // User info
            fputcsv($file, ['Khách hàng:', $user->full_name]);
            fputcsv($file, ['Email:', $user->email]);
            fputcsv($file, ['']);
            fputcsv($file, ['Mã đơn', 'Sự kiện', 'Số vé', 'Tổng tiền', 'Trạng thái', 'Ngày đặt']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->event->title ?? 'N/A',
                    $order->tickets->count(),
                    $order->final_amount,
                    $order->status,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

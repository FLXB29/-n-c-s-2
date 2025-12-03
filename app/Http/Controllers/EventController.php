<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\TicketType;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    //request là lấy thông tin cụ thể là name và value từ form bằng phương thức GET,POST,...
    public function index(Request $request)
    {
        $query = Event::published()->with(['category', 'organizer']);
        
        // Search
        if ($request->filled('search')) {
            $query->whereFullText(['title', 'description', 'venue_name', 'venue_city'], $request->search);
        }
        
        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // City filter
        if ($request->filled('city')) {
            $query->where('venue_city','like','%'.$request->city.'%');
        }
        
        // Date filter
        if ($request->filled('start_date') || $request->filled('end_date')) {
            
            if ($request->filled('start_date')) {
                $query->whereDate('start_datetime', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('start_datetime', '<=', $request->end_date);
            }
            
        } elseif ($request->filled('date')) {
            // Nếu không nhập ngày tùy chỉnh thì mới xét đến các nút chọn nhanh
            switch ($request->date) {
                case 'today':
                    $query->whereDate('start_datetime', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('start_datetime', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('start_datetime', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('start_datetime', now()->month)
                          ->whereYear('start_datetime', now()->year);
                    break;
            }
        }
        
        // Price filter
        if ($request->filled('min_price')) {
            $query->where('min_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('max_price', '<=', $request->max_price);
        }
        
        // Sort
        $sortColumn = 'start_datetime';
        $sortDirection = 'asc';

        if ($request->filled('sort')){
            switch ($request->sort){
                case 'newest':
                    $sortColumn = 'created_at';
                    $sortDirection = 'desc';
                    break;
                case 'price_asc':
                    $sortColumn = 'min_price';
                    $sortDirection = 'asc';
                    break;
                case 'price_desc' :;
                    $sortColumn = 'min_price';
                    $sortDirection = 'desc';
                    break;
                case 'upcoming':
                default:
                    $sortColumn ="start_datetime";
                    $sortDirection = 'asc';
                    break;
            }
        }
        $query->orderBy($sortColumn, $sortDirection);
        // $sortBy = $request->get('sort', 'start_datetime');
        // $sortDirection = $request->get('direction', 'asc');
        // $query->orderBy($sortBy, $sortDirection);
        
        $events = $query->paginate(1);
        $categories = Category::active()->ordered()->withCount(['events' => function ($query) {
            $query->where('status', 'published');
        }])->get();
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('events._list', compact('events'))->render(),
                'total' => $events->total()
            ]);
        }

        return view('events.index', compact('events', 'categories'));
    }
    //lấy thông tin động từ url chẳng hạn như evemts/{event} thì {event} chính là id ở đây
    public function show($id)
    {
        $event = Event::where('id', $id)->orWhere('slug', $id)->firstOrFail();

        // Increment view count
        $event->increment('view_count');
        
        //ở đây các 'category' hay 'organizer' là các mối quan hệ được định sẵn trong Event.model rồi
        $event->load(['category', 'organizer', 'ticketTypes' => function ($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }]);
        
        $relatedEvents = Event::published()
            ->where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->limit(4)
            ->get();
            
        return view('events.show', compact('event', 'relatedEvents'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('organizer.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:255',
            'venue_city' => 'required|string|max:100',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gte:min_price',
        ]);

        $data = $request->all();// lấy hết name và value từ form nên để name trong form = name trong database luôn
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        $data['organizer_id'] = Auth::id();
        // $data['status'] = 'published'; // CŨ: Tự động publish
        $data['status'] = 'pending'; // MỚI: Chờ admin duyệt

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');//tự lưu vào public/events và tự tạo tên file ngẫu nhiên
            $data['featured_image'] = 'storage/' . $path;
        }

        $event = Event::create($data);

        return redirect()->route('organizer.events.setup', $event->id)->with('success', 'Sự kiện đã được tạo! Hãy thiết lập vé và ghế ngồi.');
    }

    public function setup(Event $event)
    {
        if ($event->organizer_id != Auth::id()) {
            abort(403);
        }

        // Load existing data
        $event->load(['ticketTypes', 'seats']);

        // Reconstruct Zones from Seats
        $zones = [];
        if ($event->seats->count() > 0) {
            // Group by Section and TicketType
            $groupedSeats = $event->seats->groupBy(function($item) {
                return $item->section . '|' . $item->ticket_type_id;
            });

            $zoneIndex = 0;
            foreach ($groupedSeats as $key => $seats) {
                list($sectionName, $ticketTypeId) = explode('|', $key);
                
                // Find Ticket Type Name
                $ticketType = $event->ticketTypes->find($ticketTypeId);
                $ticketName = $ticketType ? $ticketType->name : 'Unknown';

                // Calculate Row Count and Seats Per Row
                $rows = $seats->pluck('row_number')->unique();
                $rowCount = $rows->count();
                
                // Assuming symmetric rows, take max seat number
                $maxSeat = $seats->max(function ($seat) {
                    return (int)$seat->seat_number;
                });

                // Determine Row Label (First Row)
                $firstRow = $rows->sort()->first();

                $zones[] = [
                    'id' => $zoneIndex++,
                    'ticketKey' => $ticketTypeId, // Use ID as key for existing
                    'ticketName' => $ticketName,
                    'name' => $sectionName,
                    'rowLabel' => $firstRow,
                    'rowCount' => $rowCount,
                    'seatsPerRow' => $maxSeat,
                    'totalSeats' => $seats->count()
                ];
            }
        }

        return view('organizer.events.setup', compact('event', 'zones'));
    }

    public function storeSetup(Request $request, Event $event)
    {
        if ($event->organizer_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'tickets.*.name' => 'required|string',
            'tickets.*.price' => 'required|numeric|min:0',
            'tickets.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // WARNING: This is a destructive update for simplicity. 
            // In a real app, you should check for existing Orders before deleting.
            // For this project, we assume if you re-setup, you reset everything.
            
            // Check if any tickets have been sold
            // if ($event->orders()->exists()) {
            //     throw new \Exception('Không thể thay đổi cấu hình vé/ghế vì đã có người mua vé.');
            // }

            // Delete existing Seats and TicketTypes
            Seat::where('event_id', $event->id)->delete();
            TicketType::where('event_id', $event->id)->delete();

            // 1. Create Ticket Types
            $ticketTypeMap = []; // Map temporary ID (or index) to real DB ID
            
            foreach ($request->tickets as $key => $ticketData) {
                $ticketType = TicketType::create([
                    'event_id' => $event->id,
                    'name' => $ticketData['name'],
                    'price' => $ticketData['price'],
                    'quantity' => $ticketData['quantity'],
                    'is_active' => true,
                ]);
                $ticketTypeMap[$key] = $ticketType->id;
            }

            // 2. Generate Seats (if zones provided)
            if ($request->has('zones')) {
                foreach ($request->zones as $zone) {
                    // If ticket_key is numeric (existing ID) or index, we need to handle it.
                    // The UI sends ticket_key corresponding to the tickets array index.
                    $ticketTypeId = $ticketTypeMap[$zone['ticket_key']] ?? null;
                    
                    if (!$ticketTypeId) continue;

                    $sectionName = $zone['name'] ?? 'General';
                    $rowLabel = $zone['row_label']; 
                    $rowCount = (int)$zone['row_count'];
                    $seatsPerRow = (int)$zone['seats_per_row'];

                    $isLetter = !is_numeric($rowLabel);
                    
                    for ($r = 0; $r < $rowCount; $r++) {
                        $currentRow = '';
                        if ($isLetter) {
                            $currentRow = chr(ord($rowLabel) + $r);
                        } else {
                            $currentRow = (string)((int)$rowLabel + $r);
                        }

                        for ($i = 1; $i <= $seatsPerRow; $i++) {
                            Seat::create([
                                'event_id' => $event->id,
                                'ticket_type_id' => $ticketTypeId,
                                'section' => $sectionName,
                                'row_number' => $currentRow,
                                'seat_number' => (string)$i,
                                'status' => 'available',
                            ]);
                        }
                    }
                }
            }

            // 3. Update Event Total Capacity (Total Tickets)
            $totalCapacity = TicketType::where('event_id', $event->id)->sum('quantity');
            $event->update(['total_tickets' => $totalCapacity]);

            DB::commit();
            return redirect()->route('organizer.dashboard')->with('success', 'Cập nhật thiết lập vé và ghế ngồi thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function getSeats(Event $event){
        $seats = $event->seats()
        ->orderBy('row_number')
        ->orderByRaw('CAST(seat_number AS UNSIGNED)')
        ->get()
        ->groupBy('row_number');
    return response()->json($seats);

    }

    public function edit(Event $event)
    {
        if ($event->organizer_id != Auth::id()) {
            abort(403);
        }
        $categories = Category::active()->get();
        return view('organizer.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        if ($event->organizer_id != Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'venue_name' => 'required|string|max:255',
            'venue_address' => 'required|string|max:255',
            'venue_city' => 'required|string|max:100',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gte:min_price',
        ]);

        $data = $request->all();
        
        // Only update slug if title changed? Or keep it to avoid breaking links? 
        // Usually better to keep slug or update it. Let's update it for now but handle uniqueness if needed.
        // Actually, changing slug breaks SEO and bookmarks. Let's keep slug unless explicitly requested.
        // But if title changes significantly, slug mismatch is weird.
        // For simplicity, let's NOT update slug on edit.
        unset($data['slug']); 

        if ($request->hasFile('featured_image')) {
            // Delete old image if exists and is not a URL
            if ($event->featured_image && !Str::startsWith($event->featured_image, 'http')) {
                // Storage::delete(...) - need to import Storage or just leave it (garbage collection later)
            }
            
            $path = $request->file('featured_image')->store('events', 'public');
            $data['featured_image'] = 'storage/' . $path;
        } else {
            unset($data['featured_image']);
        }

        $event->update($data);

        return redirect()->route('organizer.dashboard')->with('success', 'Cập nhật sự kiện thành công!');
    }

    public function destroy(Event $event)
    {
        if ($event->organizer_id != Auth::id()) {
            abort(403);
        }

        $event->delete(); // Soft delete because of SoftDeletes trait

        return redirect()->route('organizer.dashboard')->with('success', 'Đã xóa sự kiện thành công!');
    }
}
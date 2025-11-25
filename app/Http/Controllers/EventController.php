<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
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
        
        $events = $query->paginate(12);
        $categories = Category::active()->ordered()->get();
        
        return view('events.index', compact('events', 'categories'));
    }
    
    public function show(Event $event)
    {
        // Increment view count
        $event->increment('view_count');
        
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
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gte:min_price',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        $data['organizer_id'] = Auth::id();
        $data['status'] = 'published'; 

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('events', 'public');
            $data['banner_image'] = 'storage/' . $path;
        }

        Event::create($data);

        return redirect()->route('organizer.dashboard')->with('success', 'Tạo sự kiện thành công!');
    }

    public function getSeats(Event $event){
        $seats = $event->seats()
        ->orderBy('row_number')
        ->orderByRaw('CAST(seat_number AS UNSIGNED)')
        ->get()
        ->groupBy('row_number');
    return response()->json($seats);

    }
}
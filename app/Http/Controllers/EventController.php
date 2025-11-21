<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

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
        $sortBy = $request->get('sort', 'start_datetime');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);
        
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
}
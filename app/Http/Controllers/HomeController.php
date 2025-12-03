<?php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredEvents = Event::published()
            // ->featured()
            // ->upcoming() // Tạm thời bỏ filter upcoming để hiển thị hết các event test
            ->orderBy('start_datetime','asc')
            ->with(['category', 'organizer'])
            ->limit(6)
            ->get();
            
        $categories = Category::active()
            ->withCount('events')
            ->ordered()
            ->get();
            
        $upcomingEvents = Event::published()
            ->upcoming()
            ->with(['category', 'organizer'])
            ->orderBy('start_datetime')
            ->limit(8)
            ->get();

        return view('home', compact('featuredEvents', 'categories', 'upcomingEvents'));
    }
}
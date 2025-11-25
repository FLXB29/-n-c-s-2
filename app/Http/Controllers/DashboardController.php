<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;

class DashboardController extends Controller
{
    public function organizer()
    {
        $user = Auth::user();
        $events = Event::where('organizer_id', $user->id)->latest()->paginate(10);
        return view('organizer.dashboard', compact('events'));
    }
}

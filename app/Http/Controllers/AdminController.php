<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Thống kê cơ bản
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'pending_events' => Event::where('status', 'pending')->count(),
            'organizer_requests' => User::where('organizer_request_status', 'pending')->count(),
        ];

        // Lấy danh sách yêu cầu nâng cấp mới nhất
        $latestRequests = User::where('organizer_request_status', 'pending')->latest('organizer_request_at')->take(5)->get();
        
        // Lấy danh sách sự kiện chờ duyệt mới nhất
        $pendingEvents = Event::where('status', 'pending')->with('organizer')->latest()->take(5)->get();

        // Data for other sections (limited)
        $users = User::latest()->take(5)->get();
        $events = Event::with('organizer')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latestRequests', 'pendingEvents', 'users', 'events'));
    }

    // --- Quản lý Sự kiện ---
    public function events(Request $request)
    {
        $query = Event::with('organizer')->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $events = $query->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function approveEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'published']);
        return back()->with('success', 'Đã duyệt sự kiện: ' . $event->title);
    }

    public function rejectEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'rejected']); // Hoặc 'draft' nếu muốn cho sửa lại
        return back()->with('success', 'Đã từ chối sự kiện: ' . $event->title);
    }

    public function suspendEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'suspended']);
        return back()->with('success', 'Đã tạm dừng sự kiện: ' . $event->title);
    }

    public function restoreEvent($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'published']);
        return back()->with('success', 'Đã khôi phục sự kiện: ' . $event->title);
    }

    // --- Quản lý Yêu cầu Organizer ---
    public function organizerRequests()
    {
        $requests = User::where('organizer_request_status', 'pending')
            ->orderBy('organizer_request_at', 'asc')
            ->paginate(10);
            
        return view('admin.requests.index', compact('requests'));
    }

    public function approveOrganizer($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'role' => 'organizer',
            'organizer_request_status' => 'approved'
        ]);
        
        return back()->with('success', 'Đã chấp thuận yêu cầu nâng cấp của ' . $user->name);
    }

    public function rejectOrganizer($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'organizer_request_status' => 'rejected'
        ]);
        
        return back()->with('success', 'Đã từ chối yêu cầu của ' . $user->name);
    }

    // --- Quản lý Users ---
    public function users(Request $request)
    {
        $query = User::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // --- Giữ lại các hàm cũ nếu cần ---
    public function toggleRole($id)
    {
        $user = User::findOrFail($id);
        
        // Không thể thay đổi quyền của admin khác (nếu có)
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể thay đổi quyền của Admin.');
        }

        if ($user->role === 'user') {
            $user->role = 'organizer';
            $message = 'Đã cấp quyền Organizer cho ' . $user->name;
        } elseif ($user->role === 'organizer') {
            $user->role = 'user';
            $message = 'Đã hủy quyền Organizer của ' . $user->name;
        } else {
            return back();
        }
        
        $user->save();
        return back()->with('success', $message);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể chặn Admin.');
        }
        
        if ($user->status === 'active') {
            $user->status = 'blocked';
            $message = 'Đã chặn người dùng ' . $user->name;
        } else {
            $user->status = 'active';
            $message = 'Đã bỏ chặn người dùng ' . $user->name;
        }
        
        $user->save();
        
        return back()->with('success', $message);
    }
}

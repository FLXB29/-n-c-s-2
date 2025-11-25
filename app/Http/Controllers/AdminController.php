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
            'total_organizers' => User::where('role', 'organizer')->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        // Lấy tất cả user trừ admin
        $users = User::where('role', '!=', 'admin')->orderBy('created_at', 'desc')->get();
        
        // Lấy danh sách events (demo)
        $events = Event::with('organizer')->orderBy('created_at', 'desc')->take(10)->get();

        return view('admin.dashboard', compact('users', 'stats', 'events'));
    }

    public function toggleRole($id)
    {
        $user = User::findOrFail($id);
        
        // Không thể thay đổi quyền của admin khác (nếu có)
        if ($user->role === 'admin') {
            return back()->with('error', 'Không thể thay đổi quyền của Admin.');
        }

        if ($user->role === 'user') {
            $user->role = 'organizer';
            $message = 'Đã cấp quyền Organizer cho ' . $user->full_name;
        } elseif ($user->role === 'organizer') {
            $user->role = 'user';
            $message = 'Đã hủy quyền Organizer của ' . $user->full_name;
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
            $message = 'Đã chặn người dùng ' . $user->full_name;
        } else {
            $user->status = 'active';
            $message = 'Đã bỏ chặn người dùng ' . $user->full_name;
        }
        
        $user->save();
        
        return back()->with('success', $message);
    }
}

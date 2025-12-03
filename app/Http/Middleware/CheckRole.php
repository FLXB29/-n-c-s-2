<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Xử lý kiểm tra role của user
     * 
     * @param Request $request
     * @param Closure $next
     * @param string $role - Role cần kiểm tra (admin, organizer, user)
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Chưa đăng nhập
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = auth()->user();

        // Kiểm tra role
        if ($user->role !== $role) {
            // Redirect về dashboard phù hợp với role của user
            return $this->redirectToDashboard($user, 'Bạn không có quyền truy cập trang này.');
        }

        return $next($request);
    }

    /**
     * Redirect user về dashboard phù hợp với role
     */
    protected function redirectToDashboard($user, $message)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('error', $message);
            case 'organizer':
                return redirect()->route('organizer.dashboard')->with('error', $message);
            default:
                return redirect()->route('home')->with('error', $message);
        }
    }
}
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Event;
use App\Models\User; // Hoặc Model lưu request organizer

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();

        // Chỉ chia sẻ dữ liệu này cho file sidebar
    View::composer('admin.partials.sidebar', function ($view) {
        $sidebarStats = [
            'pending_events' => Event::where('status', 'pending')->count(),
            // Giả sử logic đếm request organizer của bạn:
            'organizer_requests' => User::where('organizer_request_status', 'pending')->count(),
        ];
        
        $view->with('stats', $sidebarStats);
    });
    }
}

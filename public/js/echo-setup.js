/**
 * Laravel Echo Setup for Realtime Seat Selection
 * Sử dụng Laravel Websockets (self-hosted, miễn phí)
 */

// Load Pusher từ CDN (đã include trong blade)
window.Echo = null;

function initEcho() {
    if (typeof Pusher === 'undefined') {
        console.error('Pusher library not loaded!');
        return;
    }

    // Cấu hình Echo
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: window.PUSHER_APP_KEY || 'eventhub-key',
        wsHost: window.PUSHER_HOST || '127.0.0.1',
        wsPort: window.PUSHER_PORT || 6001,
        wssPort: window.PUSHER_PORT || 6001,
        forceTLS: false,
        encrypted: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1',
    });

    console.log('Laravel Echo initialized successfully!');
}

// Initialize khi DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Đợi một chút để Pusher load
    setTimeout(initEcho, 100);
});

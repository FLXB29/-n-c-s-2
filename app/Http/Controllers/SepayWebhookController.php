<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SepayWebhookController extends Controller
{
    /**
     * Kiểm tra Token bất chấp mọi format (Apikey, Bearer, Token=...)
     */
    protected function validateToken(Request $request): bool
    {
        $myToken = env('SEPAY_WEBHOOK_TOKEN');
        if (!$myToken) return true; // Nếu chưa set env thì cho qua (hoặc return false tùy bạn)

        // 1. Kiểm tra URL query param (?token=...)
        if ($request->query('token') === $myToken) return true;

        // 2. Kiểm tra Header Authorization / X-Sepay-Token
        $header = $request->header('Authorization') ?? $request->header('X-SEPAY-TOKEN');
        
        if (!$header) return false;

        // "Tuyệt chiêu": Chỉ cần trong header có chứa đúng mã bí mật là cho qua
        // Bất chấp phía trước là "Apikey", "Bearer", hay "Apikey Bearer"
        return str_contains($header, $myToken);
    }

    public function handle(Request $request)
    {
        // --- LOG DATA ĐỂ DEBUG ---
        Log::info('--- SEPAY WEBHOOK START ---');
        
        // 1. Check Token
        if (!$this->validateToken($request)) {
            Log::error('Auth Failed. Header: ' . $request->header('Authorization'));
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        $content = $payload['content'] ?? $payload['description'] ?? '';
        $amount = $payload['transferAmount'] ?? $payload['amount'] ?? 0;
        $transactionId = $payload['id'] ?? $payload['transaction_id'] ?? null;

        // 2. Tìm mã đơn hàng thông minh
        $orderPrefix = env('SEPAY_ORDER_PREFIX', 'ORD'); // VD: ORD
        
        // Lấy mã thô từ tin nhắn (VD: ORDONGYHBXQ61)
        $rawOrderCode = $this->extractOrderCode($content, $orderPrefix);

        if (!$rawOrderCode) {
            Log::warning('No order code found in content: ' . $content);
            return response()->json(['success' => false, 'message' => 'Order code not found'], 200);
        }

        // 3. Tìm trong Database (Thử cả 2 trường hợp: Có gạch và Không gạch)
        // Ví dụ: Tìm 'ORDONGYHBXQ61' hoặc 'ORD-ONGYHBXQ61'
        $order = Order::where('order_code', $rawOrderCode)
            ->orWhere('order_code', $this->normalizeOrderCode($rawOrderCode, $orderPrefix)) // Thử thêm dấu gạch
            ->orWhere('order_number', $rawOrderCode)
            ->first();

        if (!$order) {
            Log::warning("Order not found in DB. Searched for: $rawOrderCode");
            return response()->json(['success' => false, 'message' => 'Order not found in DB'], 200);
        }

        if ($order->status === 'paid' || $order->payment_status === 'paid') {
            return response()->json(['success' => true, 'message' => 'Order already paid']);
        }

        // 4. Kiểm tra số tiền
        if ((float)$amount < (float)$order->final_amount) {
            Log::warning("Insufficient amount. Order: {$order->final_amount}, Received: $amount");
            return response()->json(['success' => false, 'message' => 'Amount insufficient'], 200);
        }

        // 5. Update thành công (Trạng thái vé là 'active' như đã fix)
        $order->status = 'paid';
        $order->payment_status = 'paid';
        if ($transactionId) $order->transaction_id = $transactionId;
        $order->save();

        if (method_exists($order, 'tickets')) {
            $order->tickets()->update(['status' => 'active']);
        }

        Log::info("Order {$order->order_code} paid successfully.");
        return response()->json(['success' => true]);
    }

    public function checkStatus(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) return response()->json(['status' => 'not_found'], 404);
        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment_status,
        ]);
    }

    private function extractOrderCode(string $content, string $prefix): ?string
    {
        // Regex tìm chuỗi bắt đầu bằng Prefix, chấp nhận dính liền hoặc có gạch
        // VD prefix ORD -> bắt được ORD123, ORD-123, ORD_123
        if (preg_match('/(' . preg_quote($prefix, '/') . '[-_]?[A-Z0-9]+)/i', $content, $matches)) {
            return $matches[1];
        }
        return null;
    }

    // Hàm hỗ trợ thêm dấu gạch ngang nếu mã tìm được bị dính liền
    private function normalizeOrderCode($code, $prefix) {
        // Nếu code là ORD123 -> Trả về ORD-123 để khớp với DB
        if (strpos($code, '-') === false && strpos($code, '_') === false) {
            // Chèn dấu - sau prefix
            return substr_replace($code, '-', strlen($prefix), 0);
        }
        return $code;
    }
}
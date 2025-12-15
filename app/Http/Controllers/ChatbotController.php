<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private $apiKey;
    private $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Hiển thị giao diện chatbot
     */
    public function index()
    {
        return view('chatbot.index');
    }

    /**
     * Xử lý tin nhắn từ người dùng
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->message;

        try {
            // Lấy thông tin sự kiện từ database
            $eventsContext = $this->getEventsContext();

            // Tạo prompt với context
            $prompt = $this->buildPrompt($userMessage, $eventsContext);

            // Gọi API Gemini
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể trả lời câu hỏi này.';

                return response()->json([
                    'success' => true,
                    'message' => $aiResponse,
                ]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Đã có lỗi xảy ra khi kết nối với AI. Vui lòng thử lại sau.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Lấy thông tin sự kiện từ database
     */
    private function getEventsContext()
    {
        $events = Event::where('status', 'approved')
            ->where('start_datetime', '>', now())
            ->with(['category', 'ticketTypes'])
            ->orderBy('start_datetime', 'asc')
            ->take(20)
            ->get();

        $context = "Danh sách sự kiện hiện có:\n\n";

        foreach ($events as $event) {
            $context .= "- Tên: {$event->title}\n";
            $context .= "  ID: {$event->id}\n";
            $context .= "  Mô tả: " . strip_tags($event->description) . "\n";
            $context .= "  Danh mục: " . ($event->category->name ?? 'Chưa phân loại') . "\n";
            $context .= "  Thời gian: " . $event->start_datetime->format('d/m/Y H:i') . "\n";
            $context .= "  Địa điểm: {$event->venue_name}, {$event->venue_address}\n";
            
            if ($event->ticketTypes->isNotEmpty()) {
                $context .= "  Vé:\n";
                foreach ($event->ticketTypes as $ticket) {
                    $context .= "    + {$ticket->name}: " . number_format($ticket->price) . " VNĐ (Còn {$ticket->quantity_available} vé)\n";
                }
            }
            
            $context .= "\n";
        }

        return $context;
    }

    /**
     * Tạo prompt cho AI
     */
    private function buildPrompt($userMessage, $eventsContext)
    {
        return <<<PROMPT
Bạn là trợ lý AI thông minh của hệ thống đặt vé sự kiện. Nhiệm vụ của bạn là:
1. Trả lời các câu hỏi về sự kiện một cách chính xác dựa trên dữ liệu được cung cấp
2. Gợi ý sự kiện phù hợp với nhu cầu của người dùng
3. Cung cấp thông tin về giá vé, thời gian, địa điểm
4. Hướng dẫn người dùng cách đặt vé
5. Trả lời bằng tiếng Việt một cách thân thiện và chuyên nghiệp

Dữ liệu sự kiện hiện có:
{$eventsContext}

Câu hỏi của người dùng: {$userMessage}

Hãy trả lời câu hỏi một cách ngắn gọn, rõ ràng và hữu ích. Nếu người dùng hỏi về sự kiện cụ thể, hãy đưa ra thông tin chi tiết. Nếu họ đang tìm kiếm sự kiện, hãy gợi ý các sự kiện phù hợp.
PROMPT;
    }

    /**
     * Lấy gợi ý sự kiện
     */
    public function getSuggestions(Request $request)
    {
        $query = $request->input('query', '');

        $events = Event::where('status', 'published')
            ->where('start_datetime', '>', now())
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with('category')
            ->orderBy('start_datetime', 'asc')
            ->take(3)
            ->get();

        return response()->json([
            'success' => true,
            'events' => $events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'category' => $event->category->name ?? 'Chưa phân loại',
                    'start_datetime' => $event->start_datetime->format('d/m/Y H:i'),
                    'venue_name' => $event->venue_name,
                ];
            }),
        ]);
    }
}

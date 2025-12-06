<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display chat interface
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get admin user with ID 9
        $admin = User::where('role', 'admin')->where('id', 9)->first();
        
        if (!$admin) {
            return redirect()->back()->with('error', 'Admin không tồn tại trong hệ thống.');
        }
        
        // Get the user to chat with from query parameter
        $chatWithUserId = $request->query('user_id');
        $chatWithUser = null;
        
        if ($chatWithUserId) {
            $chatWithUser = User::find($chatWithUserId);
        }
        
        // If user is admin, get all conversations
        if ($user->role === 'admin') {
            // Get users who have chatted with admin
            $conversations = Message::where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->with(['sender', 'receiver'])
                ->latest()
                ->get()
                ->groupBy(function($message) use ($user) {
                    return $message->sender_id === $user->id 
                        ? $message->receiver_id 
                        : $message->sender_id;
                })
                ->map(function($messages) {
                    return $messages->first();
                });
                
            return view('chat.index', compact('conversations', 'admin', 'chatWithUser'));
        }
        
        // For regular users/organizers
        return view('chat.index', compact('admin', 'chatWithUser'));
    }
    
    /**
     * Get messages between two users
     */
    public function getMessages($userId)
    {
        $currentUser = Auth::user();
        $targetUser = User::find($userId);
        
        if (!$targetUser) {
            return response()->json(['error' => 'User không tồn tại.'], 404);
        }
        
        // Security checks
        if ($currentUser->role === 'admin') {
            // Admin can chat with anyone
        } elseif ($currentUser->role === 'organizer') {
            // Organizer can chat with admin or users who have tickets to their events
            $admin = User::where('role', 'admin')->where('id', 9)->first();
            if ($userId != $admin->id && $targetUser->role !== 'user') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin hoặc khách hàng.'], 403);
            }
        } else {
            // Regular users can chat with admin or event organizers
            $admin = User::where('role', 'admin')->where('id', 9)->first();
            $isAdmin = ($userId == $admin->id);
            $isOrganizer = ($targetUser->role === 'organizer');
            
            if (!$isAdmin && !$isOrganizer) {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin hoặc nhà tổ chức sự kiện.'], 403);
            }
        }
        
        $messages = Message::between($currentUser->id, $userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark messages as read
        Message::where('receiver_id', $currentUser->id)
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return response()->json($messages);
    }
    
    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000'
        ]);
        
        $currentUser = Auth::user();
        $receiver = User::find($request->receiver_id);
        
        if (!$receiver) {
            return response()->json(['error' => 'Người nhận không tồn tại.'], 404);
        }
        
        // Security checks
        if ($currentUser->role === 'admin') {
            // Admin can chat with anyone
        } elseif ($currentUser->role === 'organizer') {
            // Organizer can chat with admin or users
            $admin = User::where('role', 'admin')->where('id', 9)->first();
            if ($request->receiver_id != $admin->id && $receiver->role !== 'user') {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin hoặc khách hàng.'], 403);
            }
        } else {
            // Regular users can chat with admin or organizers
            $admin = User::where('role', 'admin')->where('id', 9)->first();
            $isAdmin = ($request->receiver_id == $admin->id);
            $isOrganizer = ($receiver->role === 'organizer');
            
            if (!$isAdmin && !$isOrganizer) {
                return response()->json(['error' => 'Bạn chỉ có thể chat với admin hoặc nhà tổ chức sự kiện.'], 403);
            }
        }
        
        $message = Message::create([
            'sender_id' => $currentUser->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false
        ]);
        
        $message->load(['sender', 'receiver']);
        
        // Broadcast message
        broadcast(new MessageSent($message))->toOthers();
        
        return response()->json($message, 201);
    }
    
    /**
     * Get unread message count
     */
    public function getUnreadCount()
    {
        $userId = Auth::id();
        
        $count = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
    
    /**
     * Mark message as read
     */
    public function markAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $message->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Get conversation list for admin and organizers
     */
    public function getConversations()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin' && $user->role !== 'organizer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get all unique user IDs who have sent or received messages from/to admin
        $senderIds = Message::where('receiver_id', $user->id)
            ->distinct()
            ->pluck('sender_id');
            
        $receiverIds = Message::where('sender_id', $user->id)
            ->distinct()
            ->pluck('receiver_id');
            
        $userIds = $senderIds->merge($receiverIds)->unique();
            
        $users = User::whereIn('id', $userIds)
            ->get()
            ->map(function($u) use ($user) {
                // Get last message in conversation
                $lastMessage = Message::where(function($query) use ($user, $u) {
                    $query->where('sender_id', $user->id)
                        ->where('receiver_id', $u->id);
                })
                ->orWhere(function($query) use ($user, $u) {
                    $query->where('sender_id', $u->id)
                        ->where('receiver_id', $user->id);
                })
                ->latest()
                ->first();
                
                // Count unread messages from this user
                $unreadCount = Message::where('sender_id', $u->id)
                    ->where('receiver_id', $user->id)
                    ->where('is_read', false)
                    ->count();
                    
                $u->unread_count = $unreadCount;
                $u->last_message = $lastMessage;
                $u->last_message_time = $lastMessage ? $lastMessage->created_at : null;
                
                return $u;
            })
            ->sortByDesc('last_message_time')
            ->values();
            
        return response()->json($users);
    }
}

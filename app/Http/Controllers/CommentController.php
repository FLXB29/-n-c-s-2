<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    // Tạo comment mới
    public function store(Request $request, Event $event)
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để comment');
        }

        // Kiểm tra sự kiện có cho phép comment không
        if (!$event->allow_comments) {
            return back()->with('error', 'Sự kiện này không cho phép bình luận');
        }

        // Validate
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:500',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:event_comments,id',
        ], [
            'content.required' => 'Vui lòng nhập bình luận',
            'content.min' => 'Bình luận phải ít nhất 3 ký tự',
            'content.max' => 'Bình luận không quá 500 ký tự',
            'rating.integer' => 'Xếp hạng phải là một số',
            'rating.min' => 'Xếp hạng từ 1 đến 5 sao',
            'rating.max' => 'Xếp hạng từ 1 đến 5 sao',
        ]);

        // Tạo comment
        $comment = Comment::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'rating' => $validated['rating'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Load user info
        $comment->load('user');

        if ($request->expectsJson()) {
            $avatarUrl = $comment->user->avatar;
            if ($avatarUrl && !Str::startsWith($avatarUrl, 'http')) {
            // Áp dụng hàm asset() của Laravel cho đường dẫn nội bộ
            $avatarUrl = asset($avatarUrl); 
            }
            return response()->json([
                'success' => true,
                'message' => 'Bình luận của bạn đã được thêm thành công!',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'rating' => $comment->rating,
                    'parent_id' => $comment->parent_id,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $comment->user->id,
                        'full_name' => $comment->user->full_name,
                        'avatar' => $avatarUrl,
                    ]
                ]
            ], 201);
        }

        return back()->with('success', 'Bình luận của bạn đã được thêm thành công!');
    }

    // Cập nhật comment
    public function update(Request $request, Comment $comment)
    {
        // Kiểm tra quyền
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền chỉnh sửa bình luận này'], 403);
            }
            return back()->with('error', 'Bạn không có quyền chỉnh sửa bình luận này');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:3|max:500',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $comment->update([
            'content' => $validated['content'],
            'rating' => $validated['rating'] ?? $comment->rating,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bình luận thành công',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'rating' => $comment->rating,
                ]
            ]);
        }

        return back()->with('success', 'Cập nhật bình luận thành công');
    }

    // Xóa comment
    public function destroy(Comment $comment)
    {
        // Kiểm tra quyền
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa bình luận này'], 403);
            }
            return back()->with('error', 'Bạn không có quyền xóa bình luận này');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Xóa bình luận thành công'
            ]);
        }

        return back()->with('success', 'Xóa bình luận thành công');
    }
}
@props(['comment', 'event', 'depth' => 0])

@php
    $maxDepth = 10; // Giới hạn độ sâu tối đa
    $marginLeft = min($depth * 20, 100); // Tối đa margin 100px
@endphp

<div class="comment-item card mb-3 {{ $depth > 0 ? 'reply-item' : '' }}" 
     data-comment-id="{{ $comment->id }}" 
     style="{{ $depth > 0 ? 'margin-left: ' . $marginLeft . 'px;' : '' }}">
    <div class="card-body {{ $depth > 0 ? 'p-2' : '' }}">
        <!-- Comment Header -->
        <div class="comment-header d-flex justify-content-between align-items-start">
            <div class="comment-author">
                <div class="author-info d-flex align-items-center">
                    @if($comment->user->avatar)
                        <img 
                            src="{{ Str::startsWith($comment->user->avatar, 'http') ? $comment->user->avatar : asset($comment->user->avatar) }}" 
                            alt="{{ $comment->user->full_name }}"
                            class="avatar-sm rounded-circle me-2"
                            style="width: {{ $depth > 0 ? '32px' : '40px' }}; height: {{ $depth > 0 ? '32px' : '40px' }};"
                        >
                    @else
                        <div class="avatar-sm rounded-circle me-2 bg-secondary d-flex align-items-center justify-content-center"
                             style="width: {{ $depth > 0 ? '32px' : '40px' }}; height: {{ $depth > 0 ? '32px' : '40px' }};">
                            <i class="fas fa-user text-white" style="font-size: {{ $depth > 0 ? '12px' : '14px' }};"></i>
                        </div>
                    @endif
                    <div>
                        <strong class="author-name" style="font-size: {{ $depth > 0 ? '0.9em' : '1em' }};">
                            {{ $comment->user->full_name }}
                        </strong>
                        @if($comment->parent_id)
                            <span class="text-muted" style="font-size: 0.85em;">
                                <i class="fas fa-reply fa-flip-horizontal"></i> 
                                @if($comment->parent && $comment->parent->user)
                                    {{ $comment->parent->user->full_name }}
                                @endif
                            </span>
                        @endif
                        <small class="text-muted d-block">{{ $comment->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="comment-actions">
                @auth
                    <div class="btn-group btn-group-sm" role="group">
                        @if(Auth::id() === $comment->user_id)
                            <button type="button" class="btn btn-outline-secondary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCommentModal{{ $comment->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                        @endif

                        @if($depth < $maxDepth)
                            <button type="button" class="btn btn-outline-primary btn-sm btn-reply-comment" 
                                    data-comment-id="{{ $comment->id }}">
                                <i class="fas fa-reply"></i>
                            </button>
                        @endif

                        @if(Auth::id() === $comment->user_id || Auth::user()->role === 'admin')
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete-comment" 
                                    data-comment-id="{{ $comment->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <!-- Rating (chỉ hiển thị cho comment gốc) -->
        @if($comment->rating && $depth === 0)
            <div class="comment-rating my-2">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= $comment->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </div>
            </div>
        @endif

        <!-- Comment Content -->
        <p class="comment-content mt-2 mb-0" style="font-size: {{ $depth > 0 ? '0.95em' : '1em' }};">
            {!! nl2br(e($comment->content)) !!}
        </p>

        <!-- Reply Form -->
        @auth
            @if($depth < $maxDepth)
                <div id="replyForm{{ $comment->id }}" class="reply-form-wrapper mt-3" style="display: none;">
                    <form class="reply-form" action="{{ route('comments.store', $event->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="content" class="form-control" 
                                   placeholder="Viết trả lời cho {{ $comment->user->full_name }}..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-secondary btn-cancel-reply" type="button" 
                                    data-comment-id="{{ $comment->id }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endauth
    </div>

    <!-- Nested Replies (Đệ quy) -->
    @if($comment->replies->count() > 0)
        <div class="nested-replies">
            @foreach($comment->replies as $reply)
                <x-comment-item :comment="$reply" :event="$event" :depth="$depth + 1" />
            @endforeach
        </div>
    @endif
</div>

<!-- Edit Modal -->
@auth
    @if(Auth::id() === $comment->user_id)
        <div class="modal fade" id="editCommentModal{{ $comment->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chỉnh sửa bình luận</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form class="edit-comment-form" data-comment-id="{{ $comment->id }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="edit-content-{{ $comment->id }}">Bình luận</label>
                                <textarea 
                                    name="content" 
                                    id="edit-content-{{ $comment->id }}" 
                                    class="form-control"
                                    rows="4"
                                    required
                                >{{ $comment->content }}</textarea>
                            </div>

                            @if($depth === 0)
                                <div class="form-group mb-3">
                                    <label for="edit-rating-{{ $comment->id }}">Xếp hạng</label>
                                    <div class="rating-input edit-rating" data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-star" data-rating="1"></i>
                                        <i class="fas fa-star" data-rating="2"></i>
                                        <i class="fas fa-star" data-rating="3"></i>
                                        <i class="fas fa-star" data-rating="4"></i>
                                        <i class="fas fa-star" data-rating="5"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="edit-rating-{{ $comment->id }}" value="{{ $comment->rating ?? 0 }}">
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

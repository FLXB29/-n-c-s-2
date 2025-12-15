<!-- Comments Section -->
<section class="comments-section">
    <div class="container">
        <!-- Comments Main (Left Column) -->
        <div class="comments-main">
            <h3 class="comments-title mb-4">
                <i class="fas fa-comments"></i> Bình luận sự kiện
                <span class="badge badge-secondary">{{ $comments->count() }}</span>
            </h3>

            @auth
                <!-- Comment Form -->
                <div class="comment-form-wrapper mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Để lại bình luận của bạn</h5>
                            
                            <form action="{{ route('comments.store', $event->id) }}" method="POST">
                                @csrf
                                
                                <div class="form-group mb-3">
                                    <label for="content">Bình luận <span class="text-danger">*</span></label>
                                    <textarea 
                                        name="content" 
                                        id="content" 
                                        class="form-control @error('content') is-invalid @enderror"
                                        rows="4"
                                        placeholder="Chia sẻ ý kiến của bạn về sự kiện này..."
                                        required
                                    ></textarea>
                                    @error('content')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="rating">Xếp hạng</label>
                                    <div class="rating-input" id="ratingInput">
                                            <i class="fas fa-star" data-rating="1"></i>
                                            <i class="fas fa-star" data-rating="2"></i>
                                            <i class="fas fa-star" data-rating="3"></i>
                                            <i class="fas fa-star" data-rating="4"></i>
                                            <i class="fas fa-star" data-rating="5"></i>
                                        </div>
                                        <input type="hidden" name="rating" id="rating" value="0">
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Gửi bình luận
                                    </button>
                                </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <a href="{{ route('login') }}">Đăng nhập</a> để bình luận sự kiện này
                </div>
            @endauth

            <!-- Comments List -->
            @if($comments->count() > 0)
                <div class="comments-list">
                    @foreach($comments as $comment)
                        <x-comment-item :comment="$comment" :event="$event" :depth="0" />
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Hãy là người đầu tiên bình luận sự kiện này!
                </div>
            @endif
        </div>

        <!-- Right Sidebar (Empty - placeholder for future use) -->
        <aside class="comments-sidebar">
        </aside>
    </div>
</section>

<style>
.comments-section {
    background-color: #f8f9fa;
    padding: 40px 0;
}

.comments-section .container {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 30px;
    align-items: start;
}

.comments-section .comments-main {
    grid-column: 1;
}

.comments-section .comments-sidebar {
    grid-column: 2;
}

.comments-title {
    color: #333;
    font-weight: 600;
}

.rating-input i {
    font-size: 24px;
    cursor: pointer;
    color: #ddd;
    margin-right: 5px;
    transition: color 0.2s;
}

.rating-input i:hover,
.rating-input i.active {
    color: #ffc107;
}

.avatar-sm {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

.comment-item {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.comment-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.comment-content {
    line-height: 1.6;
    color: #555;
}

.comment-rating .stars i {
    font-size: 18px;
    margin-right: 3px;
}

/* Nested replies styling */
.nested-replies {
    margin-top: 10px;
    border-left: 3px solid #667eea;
    padding-left: 5px;
}

.nested-replies .comment-item {
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    background: linear-gradient(to right, #f8f9ff, #ffffff);
}

.nested-replies .nested-replies {
    border-left-color: #a8b5ff;
}

.nested-replies .nested-replies .nested-replies {
    border-left-color: #c8d0ff;
}

.nested-replies .nested-replies .nested-replies .nested-replies {
    border-left-color: #e0e4ff;
}

.reply-item {
    border: 1px solid #e0e0e0;
    background: #fafafa;
}

.reply-form-wrapper {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
    .nested-replies .comment-item {
        margin-left: 10px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating input
    const ratingInputs = document.querySelectorAll('.rating-input');
    
    ratingInputs.forEach(ratingInput => {
        const stars = ratingInput.querySelectorAll('i');
        let isEditMode = ratingInput.classList.contains('edit-rating');
        let commentId = isEditMode ? ratingInput.dataset.commentId : null;
        let inputSelector = isEditMode ? `#edit-rating-${commentId}` : '#rating';
        
        const hiddenInput = document.querySelector(inputSelector);
        const currentRating = hiddenInput ? parseInt(hiddenInput.value) : 0;

        stars.forEach(star => {
            if (currentRating > 0 && parseInt(star.dataset.rating) <= currentRating) {
                star.classList.add('active');
            }

            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                hiddenInput.value = rating;
                
                stars.forEach(s => {
                    if (parseInt(s.dataset.rating) <= rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            star.addEventListener('mouseover', function() {
                const rating = parseInt(this.dataset.rating);
                stars.forEach(s => {
                    if (parseInt(s.dataset.rating) <= rating) {
                        s.style.color = '#ffc107';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });

        ratingInput.addEventListener('mouseleave', function() {
            stars.forEach(s => {
                s.style.color = s.classList.contains('active') ? '#ffc107' : '#ddd';
            });
        });
    });

    // AJAX Comment Form Submit
    const commentForm = document.querySelector('form[action*="/comments"]');
    if (commentForm && !commentForm.classList.contains('edit-comment-form') && !commentForm.classList.contains('reply-form')) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const action = this.getAttribute('action');
            
            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentForm.reset();
                    document.getElementById('rating').value = 0;
                    document.querySelectorAll('#ratingInput i').forEach(star => {
                        star.classList.remove('active');
                        star.style.color = '#ddd';
                    });
                    addCommentToList(data.comment);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }

    // Delete Comment AJAX
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-delete-comment')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-delete-comment');
            const commentId = btn.dataset.commentId;
            
            if (!confirm('Bạn chắc chắn muốn xóa?')) return;
            
            fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-comment-id="${commentId}"]`).remove();
                    const badge = document.querySelector('.badge-secondary');
                    if (badge) {
                        badge.textContent = parseInt(badge.textContent) - 1;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Edit Comment AJAX
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.classList.contains('edit-comment-form')) {
            e.preventDefault();
            
            const commentId = form.dataset.commentId;
            const formData = new FormData(form);
            formData.append('_method', 'PATCH');
            
            fetch(`/comments/${commentId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const commentItem = document.querySelector(`[data-comment-id="${commentId}"]`).closest('.comment-item');
                    commentItem.querySelector('.comment-content').textContent = data.comment.content;
                    
                    // Update rating if exists
                    if (data.comment.rating) {
                        let ratingDiv = commentItem.querySelector('.comment-rating');
                        if (!ratingDiv) {
                            ratingDiv = document.createElement('div');
                            ratingDiv.className = 'comment-rating my-2';
                            ratingDiv.innerHTML = '<div class="stars"></div>';
                            commentItem.querySelector('.comment-content').parentElement.insertBefore(ratingDiv, commentItem.querySelector('.comment-content'));
                        }
                        const starsDiv = ratingDiv.querySelector('.stars');
                        starsDiv.innerHTML = Array.from({length: 5}, (_, i) => 
                            `<i class="fas fa-star ${i < data.comment.rating ? 'text-warning' : 'text-muted'}"></i>`
                        ).join('');
                    }
                    
                    const modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    if (modal) modal.hide();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Reply Comment - Toggle form
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-reply-comment')) {
            e.preventDefault();
            const btn = e.target.closest('.btn-reply-comment');
            const commentId = btn.dataset.commentId;
            const replyForm = document.querySelector(`#replyForm${commentId}`);
            
            // Ẩn tất cả các reply form khác
            document.querySelectorAll('.reply-form-wrapper').forEach(form => {
                if (form.id !== `replyForm${commentId}`) {
                    form.style.display = 'none';
                }
            });
            
            if (replyForm) {
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
                if (replyForm.style.display === 'block') {
                    replyForm.querySelector('input[name="content"]').focus();
                }
            }
        }
        
        // Cancel reply button
        if (e.target.closest('.btn-cancel-reply')) {
            const btn = e.target.closest('.btn-cancel-reply');
            const commentId = btn.dataset.commentId;
            const replyForm = document.querySelector(`#replyForm${commentId}`);
            if (replyForm) {
                replyForm.style.display = 'none';
                replyForm.querySelector('form').reset();
            }
        }
    });

    // Submit Reply
    document.addEventListener('submit', function(e) {
        const form = e.target;
        // Check nếu form nằm trong reply-form-wrapper
        const isReplyForm = form.classList.contains('reply-form') || 
                           form.closest('.reply-form-wrapper') !== null;
        
        if (isReplyForm && form.closest('.reply-form-wrapper')) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const action = form.getAttribute('action');
            
            // Find parent comment by traversing up from reply form wrapper
            const replyWrapper = form.closest('.reply-form-wrapper');
            const parentCommentDiv = replyWrapper.closest('[data-comment-id]');
            const parentId = parentCommentDiv ? parentCommentDiv.dataset.commentId : null;
            
            console.log('Reply AJAX - parentId:', parentId, 'action:', action);
            
            fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Reply response:', data);
                if (data.success && data.comment) {
                    form.reset();
                    form.closest('.reply-form-wrapper').style.display = 'none';
                    addReplyToComment(data.comment, parentId);
                } else {
                    alert('Error: ' + (data.message || 'Failed to add reply'));
                }
            })
            .catch(error => {
                console.error('Reply AJAX Error:', error);
                alert('Error adding reply: ' + error.message);
            });
        }
    });
});

function addCommentToList(comment) {
    const commentsList = document.querySelector('.comments-list');
    const noCommentsMsg = document.querySelector('.alert-info');
    
    const commentHTML = `
        <div class="comment-item card mb-3" data-comment-id="${comment.id}">
            <div class="card-body">
                <div class="comment-header d-flex justify-content-between align-items-start">
                    <div class="comment-author">
                        <div class="author-info d-flex align-items-center">
                            ${comment.user.avatar ? 
                                `<img src="${comment.user.avatar}" alt="${comment.user.full_name}" class="avatar-sm rounded-circle me-2">` :
                                `<div class="avatar-sm rounded-circle me-2 bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-white"></i>
                                </div>`
                            }
                            <div>
                                <strong class="author-name">${comment.user.full_name}</strong>
                                <small class="text-muted d-block">${comment.created_at}</small>
                            </div>
                        </div>
                    </div>
                    <div class="comment-actions">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editCommentModal${comment.id}">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-reply-comment" data-comment-id="${comment.id}">
                                <i class="fas fa-reply"></i> Trả lời
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-delete-comment" data-comment-id="${comment.id}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </div>
                </div>
                ${comment.rating ? `
                    <div class="comment-rating my-2">
                        <div class="stars">
                            ${Array.from({length: 5}, (_, i) => 
                                `<i class="fas fa-star ${i < comment.rating ? 'text-warning' : 'text-muted'}"></i>`
                            ).join('')}
                        </div>
                    </div>
                ` : ''}
                <p class="comment-content mt-3 mb-0">${comment.content}</p>
            </div>
        </div>
    `;
    
    if (noCommentsMsg) {
        noCommentsMsg.remove();
    }
    
    if (commentsList) {
        commentsList.insertAdjacentHTML('afterbegin', commentHTML);
    } else {
        const newList = document.createElement('div');
        newList.className = 'comments-list';
        newList.innerHTML = commentHTML;
        document.querySelector('.comments-main').appendChild(newList);
    }
    
    const badge = document.querySelector('.badge-secondary');
    if (badge) {
        badge.textContent = parseInt(badge.textContent) + 1;
    }
}

function addReplyToComment(reply, parentId = null) {
    const parentCommentId = parentId || reply.parent_id;
    const parentComment = document.querySelector(`[data-comment-id="${parentCommentId}"]`);
    if (!parentComment) return;
    
    // Tìm hoặc tạo nested-replies container
    let nestedReplies = parentComment.querySelector(':scope > .nested-replies');
    if (!nestedReplies) {
        nestedReplies = document.createElement('div');
        nestedReplies.className = 'nested-replies';
        parentComment.appendChild(nestedReplies);
    }
    
    const replyHTML = `
        <div class="comment-item card mb-3 reply-item" data-comment-id="${reply.id}" style="margin-left: 20px;">
            <div class="card-body p-2">
                <div class="comment-header d-flex justify-content-between align-items-start">
                    <div class="author-info d-flex align-items-center">
                        ${reply.user.avatar ? 
                            `<img src="${reply.user.avatar}" alt="${reply.user.full_name}" class="avatar-sm rounded-circle me-2" style="width: 32px; height: 32px;">` :
                            `<div class="avatar-sm rounded-circle me-2 bg-secondary d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                            </div>`
                        }
                        <div>
                            <strong style="font-size: 0.9em;">${reply.user.full_name}</strong>
                            <small class="text-muted d-block">${reply.created_at}</small>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm btn-reply-comment" data-comment-id="${reply.id}">
                            <i class="fas fa-reply"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-comment" data-comment-id="${reply.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="comment-content mt-2 mb-0" style="font-size: 0.95em;">${reply.content}</p>
                
                <!-- Reply Form cho nested reply -->
                <div id="replyForm${reply.id}" class="reply-form-wrapper mt-3" style="display: none;">
                    <form class="reply-form" action="/events/${reply.event_id}/comments" method="POST">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="parent_id" value="${reply.id}">
                        <input type="hidden" name="event_id" value="${reply.event_id}">
                        <div class="input-group input-group-sm">
                            <input type="text" name="content" class="form-control" 
                                   placeholder="Viết trả lời cho ${reply.user.full_name}..." required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-secondary btn-cancel-reply" type="button" data-comment-id="${reply.id}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    nestedReplies.insertAdjacentHTML('beforeend', replyHTML);
}
</script>
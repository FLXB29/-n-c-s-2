// ===== CHAT PAGE JAVASCRIPT =====

document.addEventListener('DOMContentLoaded', function() {
    initChat();
});

// Global variables
let currentReceiverId = null;
let messageCheckInterval = null;
let echoInstance = null;

// ===== INITIALIZATION =====
function initChat() {
    const chatData = document.getElementById('chatData');
    const userId = chatData.dataset.userId;
    const userRole = chatData.dataset.userRole;
    const adminId = chatData.dataset.adminId;
    const chatWithId = chatData.dataset.chatWithId;
    const chatWithName = chatData.dataset.chatWithName;
    const chatWithAvatar = chatData.dataset.chatWithAvatar;
    
    // Initialize Laravel Echo for real-time messaging
    initLaravelEcho(userId);
    
    // Set receiver ID for non-admin users
    if (userRole !== 'admin' && userRole !== 'organizer') {
        // If there's a specific user to chat with, use that; otherwise use admin
        if (chatWithId) {
            currentReceiverId = chatWithId;
            loadMessages(chatWithId);
            // Update header if needed
            if (chatWithName) {
                const nameEl = document.getElementById('chatUserName');
                const avatarEl = document.querySelector('.user-avatar');
                if (nameEl) nameEl.textContent = chatWithName;
                if (avatarEl && chatWithAvatar) {
                    avatarEl.innerHTML = `<img src="${chatWithAvatar}" alt="${chatWithName}" onerror="this.src='/images/default-avatar.png'">`;
                }
            }
        } else {
            currentReceiverId = adminId;
            loadMessages(adminId);
        }
    } else {
        loadConversations();
    }
    
    // Initialize event listeners
    initMessageForm();
    initMessageInput();
    initConversationRefresh();
    
    // Poll for new messages every 10 seconds as fallback
    messageCheckInterval = setInterval(() => {
        if (currentReceiverId) {
            loadMessages(currentReceiverId, true);
        }
        updateUnreadCount();
        
        // If admin or organizer, refresh conversations periodically
        if (userRole === 'admin' || userRole === 'organizer') {
            loadConversations();
        }
    }, 10000);
}

// ===== LARAVEL ECHO SETUP =====
function initLaravelEcho(userId) {
    // Check if Echo is available
    if (typeof Echo === 'undefined') {
        console.warn('Laravel Echo not loaded. Using polling fallback.');
        return;
    }
    
    try {
        const chatData = document.getElementById('chatData');
        const userRole = chatData.dataset.userRole;
        
        // Subscribe to private channel for current user
        echoInstance = Echo.private(`chat.${userId}`)
            .listen('.message.sent', (e) => {
                console.log('New message received via Echo:', e);
                
                // If this message is for current conversation, append it
                if (e.sender_id === parseInt(currentReceiverId) || e.receiver_id === parseInt(currentReceiverId)) {
                    appendMessage(e, e.sender_id === parseInt(userId));
                }
                
                playNotificationSound();
                
                // Update unread count
                updateUnreadCount();
                
                // If admin, reload conversations to show new user or update order
                if (userRole === 'admin') {
                    loadConversations();
                }
            });
            
        console.log('Laravel Echo initialized successfully');
    } catch (error) {
        console.error('Error initializing Laravel Echo:', error);
    }
}

// ===== MESSAGE FORM =====
function initMessageForm() {
    const form = document.getElementById('messageForm');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const receiverIdInput = document.getElementById('receiverId');
            const message = messageInput.value.trim();
            const receiverId = receiverIdInput.value;
            
            if (!message || !receiverId) {
                showAlert('Vui lòng nhập tin nhắn và chọn người nhận!', 'error');
                return;
            }
            
            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        receiver_id: receiverId,
                        message: message
                    })
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    appendMessage(data, true);
                    messageInput.value = '';
                    updateCharCount();
                    scrollToBottom();
                } else {
                    showAlert(data.error || 'Không thể gửi tin nhắn!', 'error');
                }
            } catch (error) {
                console.error('Error sending message:', error);
                showAlert('Lỗi kết nối! Vui lòng thử lại.', 'error');
            }
        });
    }
}

// ===== MESSAGE INPUT =====
function initMessageInput() {
    const messageInput = document.getElementById('messageInput');
    
    if (messageInput) {
        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            updateCharCount();
        });
        
        // Send on Ctrl+Enter
        messageInput.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('messageForm').dispatchEvent(new Event('submit'));
            }
        });
    }
}

function updateCharCount() {
    const messageInput = document.getElementById('messageInput');
    const charCount = document.getElementById('charCount');
    
    if (messageInput && charCount) {
        charCount.textContent = messageInput.value.length;
    }
}

// ===== LOAD MESSAGES =====
async function loadMessages(userId, silent = false) {
    if (!userId) return;
    
    try {
        const response = await fetch(`/chat/messages/${userId}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const messages = await response.json();
        
        if (response.ok) {
            displayMessages(messages, silent);
        } else {
            if (!silent) {
                showAlert(messages.error || 'Không thể tải tin nhắn!', 'error');
            }
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        if (!silent) {
            showAlert('Lỗi kết nối!', 'error');
        }
    }
}

function displayMessages(messages, silent = false) {
    const chatMessages = document.getElementById('chatMessages');
    const wasAtBottom = isScrolledToBottom();
    
    if (!silent) {
        chatMessages.innerHTML = '';
    }
    
    const chatData = document.getElementById('chatData');
    const currentUserId = parseInt(chatData.dataset.userId);
    
    messages.forEach(message => {
        const messageEl = createMessageElement(message, message.sender_id === currentUserId);
        
        if (silent) {
            // Only add if message doesn't exist
            const existingMessage = chatMessages.querySelector(`[data-message-id="${message.id}"]`);
            if (!existingMessage) {
                chatMessages.appendChild(messageEl);
            }
        } else {
            chatMessages.appendChild(messageEl);
        }
    });
    
    if (wasAtBottom || !silent) {
        scrollToBottom();
    }
}

// ===== APPEND MESSAGE =====
function appendMessage(message, isSent) {
    const chatMessages = document.getElementById('chatMessages');
    const messageEl = createMessageElement(message, isSent);
    
    chatMessages.appendChild(messageEl);
    scrollToBottom();
}

function createMessageElement(message, isSent) {
    const div = document.createElement('div');
    div.className = `message ${isSent ? 'sent' : 'received'}`;
    div.setAttribute('data-message-id', message.id);
    
    const time = new Date(message.created_at).toLocaleTimeString('vi-VN', {
        hour: '2-digit',
        minute: '2-digit'
    });
    
    div.innerHTML = `
        <div class="message-content">
            <p>${escapeHtml(message.message)}</p>
            <span class="message-time">${time}</span>
        </div>
    `;
    
    return div;
}

// ===== CONVERSATIONS (Admin and Organizer) =====
async function loadConversations() {
    const chatData = document.getElementById('chatData');
    const userRole = chatData.dataset.userRole;
    
    if (userRole !== 'admin' && userRole !== 'organizer') return;
    
    console.log('Loading conversations for', userRole, '...');
    
    try {
        const response = await fetch('/chat/conversations', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const users = await response.json();
        
        console.log('Conversations response:', users);
        
        if (response.ok) {
            displayConversations(users);
        } else {
            console.error('Error response:', users);
        }
    } catch (error) {
        console.error('Error loading conversations:', error);
    }
}

function displayConversations(users) {
    const conversationsList = document.getElementById('conversationsList');
    
    console.log('Displaying conversations:', users);
    console.log('Number of users:', users.length);
    
    if (users.length === 0) {
        conversationsList.innerHTML = `
            <div class="no-conversations">
                <i class="fas fa-inbox"></i>
                <p>Chưa có hội thoại nào</p>
                <p class="text-muted" style="font-size: 12px; margin-top: 8px;">Người dùng sẽ xuất hiện ở đây khi họ gửi tin nhắn</p>
            </div>
        `;
        return;
    }
    
    conversationsList.innerHTML = users.map(user => {
        let lastMessagePreview = '';
        if (user.last_message) {
            const msg = user.last_message.message;
            lastMessagePreview = msg.length > 30 ? msg.substring(0, 30) + '...' : msg;
        }
        
        const userName = user.full_name || user.name || 'User';
        let avatarUrl = '/images/default-avatar.png';
        if (user.avatar && user.avatar !== 'null') {
            // Check if it's an external URL (Google, Facebook, etc.)
            if (user.avatar.startsWith('http://') || user.avatar.startsWith('https://')) {
                avatarUrl = user.avatar;
            } else if (user.avatar.startsWith('storage/')) {
                // Local storage path - add leading slash
                avatarUrl = '/' + user.avatar;
            } else {
                avatarUrl = user.avatar;
            }
        }
        
        return `
        <div class="conversation-item ${currentReceiverId == user.id ? 'active' : ''}" 
             data-user-id="${user.id}"
             data-user-name="${escapeHtml(userName)}"
             data-user-avatar="${avatarUrl}"
             onclick="selectConversation(${user.id}, '${escapeHtml(userName)}', '${avatarUrl}')">
            <div class="conversation-avatar">
                <img src="${avatarUrl}" alt="${escapeHtml(userName)}" onerror="this.src='/images/default-avatar.png'">
            </div>
            <div class="conversation-info">
                <div class="conversation-header">
                    <h4>${escapeHtml(userName)}</h4>
                    ${user.last_message_time ? `<span class="conversation-time">${formatTime(user.last_message_time)}</span>` : ''}
                </div>
                ${lastMessagePreview ? `<p class="conversation-preview">${escapeHtml(lastMessagePreview)}</p>` : ''}
                <span class="conversation-role">${getRoleName(user.role)}</span>
            </div>
            ${user.unread_count > 0 ? `<span class="unread-badge">${user.unread_count}</span>` : ''}
        </div>
    `;
    }).join('');
}

function selectConversation(userId, userName, userAvatar) {
    currentReceiverId = userId;
    
    // Ensure avatar URL is properly formatted
    if (!userAvatar || userAvatar === 'null' || userAvatar === 'undefined') {
        userAvatar = '/images/default-avatar.png';
    }
    
    // Update UI
    const chatHeader = document.querySelector('.chat-user-info');
    const avatarEl = chatHeader.querySelector('.user-avatar');
    const nameEl = document.getElementById('chatUserName');
    const statusEl = document.getElementById('chatUserStatus');
    
    // Update avatar
    if (userAvatar) {
        avatarEl.innerHTML = `<img src="${userAvatar}" alt="${userName}" onerror="this.src='/images/default-avatar.png'">`;
    } else {
        avatarEl.innerHTML = '<i class="fas fa-user"></i>';
    }
    
    // Update name and status
    nameEl.textContent = userName;
    statusEl.textContent = 'Online';
    statusEl.className = 'user-status online';
    
    // Enable input
    document.getElementById('receiverId').value = userId;
    document.getElementById('messageInput').disabled = false;
    document.getElementById('sendBtn').disabled = false;
    
    // Update active conversation
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    const selectedItem = document.querySelector(`[data-user-id="${userId}"]`);
    if (selectedItem) {
        selectedItem.classList.add('active');
    }
    
    // Load messages
    loadMessages(userId);
}

function initConversationRefresh() {
    const refreshBtn = document.getElementById('refreshConversations');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', loadConversations);
    }
}

// ===== UNREAD COUNT =====
async function updateUnreadCount() {
    try {
        const response = await fetch('/chat/unread-count');
        const data = await response.json();
        
        if (data.count > 0) {
            updateUnreadBadge(data.count);
        }
    } catch (error) {
        console.error('Error updating unread count:', error);
    }
}

function updateUnreadBadge(count) {
    // Update badge in navbar or header
    const badge = document.querySelector('.chat-unread-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    }
}

// ===== UTILITY FUNCTIONS =====
function scrollToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function isScrolledToBottom() {
    const chatMessages = document.getElementById('chatMessages');
    return chatMessages.scrollHeight - chatMessages.scrollTop <= chatMessages.clientHeight + 100;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getRoleName(role) {
    const roles = {
        'admin': 'Quản trị viên',
        'organizer': 'Nhà tổ chức',
        'user': 'Người dùng'
    };
    return roles[role] || role;
}

function playNotificationSound() {
    // Play notification sound if available
    const audio = new Audio('/sounds/notification.mp3');
    audio.volume = 0.5;
    audio.play().catch(() => {}); // Ignore errors
}

// Format time helper
function formatTime(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(diff / 3600000);
    const days = Math.floor(diff / 86400000);
    
    if (minutes < 1) return 'Vừa xong';
    if (minutes < 60) return `${minutes} phút`;
    if (hours < 24) return `${hours} giờ`;
    if (days < 7) return `${days} ngày`;
    
    return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
}

// Clear chat
document.getElementById('clearChat')?.addEventListener('click', function() {
    if (confirm('Bạn có chắc muốn xóa lịch sử chat này?')) {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = `
            <div class="chat-welcome">
                <i class="fas fa-comments"></i>
                <p>Lịch sử chat đã được xóa</p>
            </div>
        `;
    }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (messageCheckInterval) {
        clearInterval(messageCheckInterval);
    }
});

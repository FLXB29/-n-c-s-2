@extends('layouts.app')

@section('title', 'Trang chủ - EventHub')

@section('content')
<!-- Hero Banner -->
<section class="hero-banner">
    <div class="banner-slider">
        @foreach($featuredEvents->take(3) as $index => $event)
        <div class="banner-slide {{ $index === 0 ? 'active' : '' }}">
            <div class="banner-image" style="background-image: url('{{ Str::startsWith($event->featured_image, 'http') ? $event->featured_image : asset($event->featured_image) }}')"></div>
            <div class="banner-content">
                <div class="container">
                    <span class="banner-category">{{ $event->category->name }}</span>
                    <h1>{{ $event->title }}</h1>
                    <p>{{ $event->short_description }}</p>
                    <div class="banner-meta">
                        <span><i class="fas fa-calendar"></i> {{ $event->start_datetime->format('d/m/Y H:i') }}</span>
                        <span><i class="fas fa-map-marker-alt"></i> {{ $event->venue_city }}</span>
                    </div>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-lg">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="banner-controls">
        <button class="banner-prev"><i class="fas fa-chevron-left"></i></button>
        <div class="banner-dots">
            @foreach($featuredEvents->take(3) as $index => $event)
            <span class="dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @endforeach
        </div>
        <button class="banner-next"><i class="fas fa-chevron-right"></i></button>
    </div>
</section>

<!-- Featured Events -->
<section class="featured-events">
    <div class="container">
        <div class="section-header">
            <h2>Sự kiện nổi bật</h2>
            <p>Khám phá những sự kiện hot nhất đang chờ đón bạn</p>
        </div>
        
        <div class="events-grid">
            @foreach($featuredEvents as $event)
            <div class="event-card">
                <div class="event-image">
                    <a href="{{route ('events.show', $event->slug)}}">
                    <img src="{{ Str::startsWith($event->featured_image, 'http') ? $event->featured_image : asset($event->featured_image) }}" alt="{{ $event->title }}">
                    <div class="event-badge">{{ $event->category->name }}</div>
                    @if($event->is_featured)
                    <div class="featured-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    @endif
                    </a>
                </div>
                <div class="event-content">
                    <h3>{{ $event->title }}</h3>
                    <div class="event-meta">
                        <div class="event-date">
                            <i class="fas fa-calendar"></i>
                            {{ $event->start_datetime->format('d/m/Y') }}
                        </div>
                        <div class="event-location">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $event->venue_city }}
                        </div>
                    </div>
                    <div class="event-price">
                        {{ $event->formatted_price }}
                    </div>
                    <a href="{{ route('events.show', $event->slug) }}" class="btn btn-primary btn-sm">
                        Mua vé ngay
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Khám phá theo danh mục</h2>
            <p>Tìm kiếm sự kiện theo sở thích của bạn</p>
        </div>
        
        <div class="categories-grid">
            @foreach($categories as $category)
            <a href="{{ route('events.index', ['category' => $category->slug]) }}" class="category-card">
                <div class="category-icon" style="background: {{ $category->color }}">
                    <i class="{{ $category->icon }}"></i>
                </div>
                <h4>{{ $category->name }}</h4>
                <span>{{ $category->events_count ?? 0 }} sự kiện</span>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Chatbot Widget -->
<div id="chatbotWidget" class="chatbot-widget">
    <div class="chatbot-button" id="chatbotToggle">
        <i class="fas fa-robot"></i>
        <span class="chatbot-badge">AI</span>
    </div>
    
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div class="chatbot-header-content">
                <i class="fas fa-robot"></i>
                <div>
                    <h4>Trợ Lý AI</h4>
                    <span class="chatbot-status">Online</span>
                </div>
            </div>
            <button class="chatbot-close" id="chatbotClose">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chatbot-message-content">
                    <p>Xin chào! 👋 Tôi có thể giúp bạn tìm sự kiện phù hợp. Bạn muốn tìm gì?</p>
                </div>
            </div>
        </div>
        
        <div class="chatbot-quick-actions">
            <button class="quick-action-btn" data-message="Có sự kiện nào sắp diễn ra không?">
                📅 Sự kiện sắp diễn ra
            </button>
            <button class="quick-action-btn" data-message="Tìm sự kiện âm nhạc">
                🎵 Sự kiện âm nhạc
            </button>
        </div>
        
        <div class="chatbot-input-area">
            <input type="text" id="chatbotInput" placeholder="Nhập câu hỏi..." autocomplete="off">
            <button id="chatbotSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.chatbot-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.chatbot-button i {
    font-size: 28px;
}

.chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4757;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
}

.chatbot-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    height: 550px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-window.active {
    display: flex;
}

.chatbot-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header-content {
    display: flex;
    align-items: center;
    gap: 10px;
}

.chatbot-header i {
    font-size: 24px;
}

.chatbot-header h4 {
    margin: 0;
    font-size: 16px;
}

.chatbot-status {
    font-size: 11px;
    opacity: 0.9;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    padding: 5px;
    font-size: 18px;
}

.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
}

.chatbot-message {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    animation: fadeIn 0.3s ease;
}

.chatbot-message.user {
    flex-direction: row-reverse;
}

.chatbot-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.chatbot-message.user .chatbot-avatar {
    background: #e0e0e0;
    color: #666;
}

.chatbot-message-content {
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 12px;
    background: white;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.chatbot-message.user .chatbot-message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.chatbot-message-content p {
    margin: 0 0 8px 0;
    font-size: 14px;
    line-height: 1.5;
}

.chatbot-message-content p:last-child {
    margin-bottom: 0;
}

.chatbot-message-content strong {
    font-weight: 600;
}

.chatbot-message-content em {
    font-style: italic;
}

.chatbot-message-content ul,
.chatbot-message-content ol {
    margin: 8px 0;
    padding-left: 20px;
}

.chatbot-message-content li {
    margin: 4px 0;
}

.chatbot-message-content h1,
.chatbot-message-content h2,
.chatbot-message-content h3 {
    font-size: 1em;
    font-weight: 600;
    margin: 8px 0 4px 0;
}

.chatbot-message-content code {
    background: rgba(0,0,0,0.08);
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 0.9em;
}

.chatbot-message.user .chatbot-message-content code {
    background: rgba(255,255,255,0.2);
}

.chatbot-quick-actions {
    padding: 10px 15px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    background: white;
    border-top: 1px solid #e0e0e0;
}

.quick-action-btn {
    padding: 6px 12px;
    border: 2px solid #667eea;
    background: white;
    color: #667eea;
    border-radius: 16px;
    font-size: 12px;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.3s;
}

.quick-action-btn:hover {
    background: #667eea;
    color: white;
}

.chatbot-input-area {
    padding: 15px;
    background: white;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 10px;
}

#chatbotInput {
    flex: 1;
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
}

#chatbotInput:focus {
    border-color: #667eea;
}

#chatbotSend {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

#chatbotSend:hover {
    transform: scale(1.1);
}

.chatbot-typing {
    display: none;
    padding: 10px 14px;
    background: white;
    border-radius: 12px;
    width: fit-content;
}

.chatbot-typing.active {
    display: block;
}

.chatbot-typing span {
    height: 6px;
    width: 6px;
    background: #667eea;
    border-radius: 50%;
    display: inline-block;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.chatbot-typing span:nth-child(2) { animation-delay: 0.2s; }
.chatbot-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-8px); }
}

@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 40px);
        right: 20px;
        left: 20px;
        height: 500px;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggle = document.getElementById('chatbotToggle');
    const chatbotWindow = document.getElementById('chatbotWindow');
    const chatbotClose = document.getElementById('chatbotClose');
    const chatbotInput = document.getElementById('chatbotInput');
    const chatbotSend = document.getElementById('chatbotSend');
    const chatbotMessages = document.getElementById('chatbotMessages');
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');

    // Toggle chatbot window
    chatbotToggle.addEventListener('click', function() {
        chatbotWindow.classList.add('active');
        chatbotInput.focus();
    });

    chatbotClose.addEventListener('click', function() {
        chatbotWindow.classList.remove('active');
    });

    // Hàm parse markdown sang HTML
    function parseMarkdown(text) {
        if (typeof marked !== 'undefined') {
            try {
                marked.setOptions({ breaks: true, gfm: true });
                return marked.parse ? marked.parse(text) : marked(text);
            } catch (e) {
                console.error('Lỗi parse markdown:', e);
            }
        }
        
        // Fallback: parse markdown thủ công
        return text
            .replace(/\*\*\*(.+?)\*\*\*/g, '<strong><em>$1</em></strong>')
            .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.+?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    // Add message to chat
    function addMessage(message, isUser = false) {
        const displayMessage = isUser ? message : parseMarkdown(message);
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${isUser ? 'user' : 'bot'}`;
        messageDiv.innerHTML = `
            <div class="chatbot-avatar">
                <i class="fas fa-${isUser ? 'user' : 'robot'}"></i>
            </div>
            <div class="chatbot-message-content">
                ${isUser ? '<p>' + message + '</p>' : displayMessage}
            </div>
        `;
        chatbotMessages.appendChild(messageDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    // Show typing indicator
    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot typing-message';
        typingDiv.innerHTML = `
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chatbot-typing active">
                <span></span><span></span><span></span>
            </div>
        `;
        chatbotMessages.appendChild(typingDiv);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }

    function hideTyping() {
        const typingMsg = chatbotMessages.querySelector('.typing-message');
        if (typingMsg) typingMsg.remove();
    }

    // Send message
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (!message) return;

        addMessage(message, true);
        chatbotInput.value = '';
        chatbotSend.disabled = true;

        showTyping();

        // Call API
        fetch('{{ route("chatbot.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            hideTyping();
            if (data.success) {
                addMessage(data.message, false);
            } else {
                addMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.', false);
            }
            chatbotSend.disabled = false;
        })
        .catch(error => {
            hideTyping();
            addMessage('Không thể kết nối với server.', false);
            chatbotSend.disabled = false;
        });
    }

    // Event listeners
    chatbotSend.addEventListener('click', sendMessage);
    chatbotInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Quick actions
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            chatbotInput.value = this.dataset.message;
            sendMessage();
        });
    });
});
</script>
@endsection
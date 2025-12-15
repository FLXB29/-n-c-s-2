<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trợ Lý AI - Tìm Sự Kiện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-container {
            width: 90%;
            max-width: 900px;
            height: 85vh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-header i {
            font-size: 28px;
        }

        .chat-header h1 {
            font-size: 22px;
            margin: 0;
            font-weight: 600;
        }

        .chat-header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #f8f9fa;
        }

        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            justify-content: flex-end;
        }

        .message-content {
            max-width: 70%;
            padding: 12px 18px;
            border-radius: 18px;
            position: relative;
        }

        .message.bot .message-content {
            background: white;
            color: #333;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .message.user .message-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-size: 18px;
        }

        .message.bot .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message.user .message-avatar {
            background: #e0e0e0;
            color: #666;
        }

        .chat-input-container {
            padding: 20px 25px;
            background: white;
            border-top: 1px solid #e0e0e0;
        }

        .chat-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #messageInput {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: border-color 0.3s;
        }

        #messageInput:focus {
            border-color: #667eea;
        }

        #sendButton {
            width: 48px;
            height: 48px;
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

        #sendButton:hover {
            transform: scale(1.05);
        }

        #sendButton:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .typing-indicator {
            display: none;
            padding: 10px 15px;
            background: white;
            border-radius: 18px;
            border: 1px solid #e0e0e0;
            width: fit-content;
        }

        .typing-indicator.active {
            display: block;
        }

        .typing-indicator span {
            height: 8px;
            width: 8px;
            background: #667eea;
            border-radius: 50%;
            display: inline-block;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        .quick-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .suggestion-chip {
            padding: 8px 15px;
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 20px;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .suggestion-chip:hover {
            background: #667eea;
            color: white;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        /* Markdown formatting styles */
        .message-content strong,
        .message-content b {
            font-weight: 600;
        }

        .message-content ul,
        .message-content ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .message-content li {
            margin-bottom: 5px;
            line-height: 1.6;
        }

        .message-content p {
            margin: 8px 0;
            line-height: 1.6;
        }

        .message-content p:first-child {
            margin-top: 0;
        }

        .message-content p:last-child {
            margin-bottom: 0;
        }

        .message-content h1,
        .message-content h2,
        .message-content h3,
        .message-content h4 {
            font-size: 1em;
            font-weight: 600;
            margin: 10px 0 5px 0;
        }

        .message-content code {
            background: rgba(0,0,0,0.1);
            padding: 2px 5px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .message.user .message-content code {
            background: rgba(255,255,255,0.2);
        }

        .message-content a {
            color: #667eea;
            text-decoration: underline;
        }

        .message.user .message-content a {
            color: #fff;
        }

        .message-content blockquote {
            border-left: 3px solid #667eea;
            padding-left: 10px;
            margin: 10px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <i class="fas fa-robot"></i>
            <div>
                <h1>Trợ Lý AI - Tìm Sự Kiện</h1>
                <p>Hỏi tôi bất cứ điều gì về các sự kiện!</p>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="message bot">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <p>Xin chào! 👋 Tôi là trợ lý AI của hệ thống đặt vé sự kiện. Tôi có thể giúp bạn:</p>
                    <ul style="margin: 10px 0 0 20px; padding: 0;">
                        <li>Tìm kiếm sự kiện phù hợp</li>
                        <li>Xem thông tin chi tiết về sự kiện</li>
                        <li>Kiểm tra giá vé và tình trạng còn vé</li>
                        <li>Gợi ý sự kiện theo sở thích của bạn</li>
                    </ul>
                    <p style="margin-top: 10px;">Bạn muốn tìm sự kiện gì hôm nay?</p>
                </div>
            </div>

            <div class="quick-suggestions">
                <button class="suggestion-chip" onclick="sendQuickMessage('Có sự kiện nào sắp diễn ra không?')">
                    📅 Sự kiện sắp diễn ra
                </button>
                <button class="suggestion-chip" onclick="sendQuickMessage('Tìm sự kiện âm nhạc')">
                    🎵 Sự kiện âm nhạc
                </button>
                <button class="suggestion-chip" onclick="sendQuickMessage('Sự kiện miễn phí')">
                    🎟️ Sự kiện miễn phí
                </button>
                <button class="suggestion-chip" onclick="sendQuickMessage('Sự kiện cuối tuần')">
                    🎪 Sự kiện cuối tuần
                </button>
            </div>
        </div>

        <div class="chat-input-container">
            <div class="chat-input-wrapper">
                <input 
                    type="text" 
                    id="messageInput" 
                    placeholder="Nhập câu hỏi của bạn..." 
                    autocomplete="off"
                />
                <button id="sendButton" type="button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        $(document).ready(function() {
            const messagesContainer = $('#chatMessages');
            const messageInput = $('#messageInput');
            const sendButton = $('#sendButton');

            // Cấu hình marked để parse markdown
            marked.setOptions({
                breaks: true,      // Cho phép xuống dòng với single line break
                gfm: true,         // GitHub Flavored Markdown
                headerIds: false,  // Tắt tự động tạo id cho headers
                mangle: false      // Không mã hóa email addresses
            });

            // Hàm parse markdown sang HTML
            function parseMarkdown(text) {
                return marked.parse(text);
            }

            // Scroll to bottom
            function scrollToBottom() {
                messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
            }

            // Add message to chat
            function addMessage(message, isUser = false) {
                // Parse markdown cho tin nhắn từ bot, giữ nguyên text cho user
                const displayMessage = isUser ? $('<div>').text(message).html() : parseMarkdown(message);
                
                const messageHtml = `
                    <div class="message ${isUser ? 'user' : 'bot'}">
                        ${!isUser ? '<div class="message-avatar"><i class="fas fa-robot"></i></div>' : ''}
                        <div class="message-content">
                            ${isUser ? '<p>' + displayMessage + '</p>' : displayMessage}
                        </div>
                        ${isUser ? '<div class="message-avatar"><i class="fas fa-user"></i></div>' : ''}
                    </div>
                `;
                messagesContainer.append(messageHtml);
                scrollToBottom();
            }

            // Show typing indicator
            function showTyping() {
                const typingHtml = `
                    <div class="message bot typing-message">
                        <div class="message-avatar"><i class="fas fa-robot"></i></div>
                        <div class="typing-indicator active">
                            <span></span><span></span><span></span>
                        </div>
                    </div>
                `;
                messagesContainer.append(typingHtml);
                scrollToBottom();
            }

            // Hide typing indicator
            function hideTyping() {
                $('.typing-message').remove();
            }

            // Send message
            function sendMessage() {
                const message = messageInput.val().trim();
                if (!message) return;

                // Add user message
                addMessage(message, true);
                messageInput.val('');
                sendButton.prop('disabled', true);

                // Show typing
                showTyping();

                // Send to server
                $.ajax({
                    url: '{{ route("chatbot.send") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        message: message
                    },
                    success: function(response) {
                        hideTyping();
                        if (response.success) {
                            addMessage(response.message, false);
                        } else {
                            addMessage('Xin lỗi, đã có lỗi xảy ra. Vui lòng thử lại.', false);
                        }
                        sendButton.prop('disabled', false);
                    },
                    error: function() {
                        hideTyping();
                        addMessage('Không thể kết nối với server. Vui lòng thử lại sau.', false);
                        sendButton.prop('disabled', false);
                    }
                });
            }

            // Event listeners
            sendButton.on('click', sendMessage);
            messageInput.on('keypress', function(e) {
                if (e.which === 13) {
                    sendMessage();
                }
            });

            // Initial focus
            messageInput.focus();
        });

        // Quick message function
        function sendQuickMessage(message) {
            $('#messageInput').val(message);
            $('#sendButton').click();
        }
    </script>
</body>
</html>

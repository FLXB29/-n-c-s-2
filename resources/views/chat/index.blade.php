@extends('layouts.app')

@section('title', 'Chat với Admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@endsection

@section('content')
<div class="chat-container">
    <div class="chat-wrapper">
        <!-- Sidebar: Conversations (Admin and Organizer) -->
        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'organizer')
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h3>Hội thoại</h3>
                <button id="refreshConversations" class="btn-icon">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="conversations-list" id="conversationsList">
                <!-- Conversations will be loaded here -->
                <div class="loading-conversations">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p>Đang tải...</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Chat Area -->
        <div class="chat-main {{ (auth()->user()->role === 'admin' || auth()->user()->role === 'organizer') ? '' : 'full-width' }}">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="chat-user-info">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'organizer')
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h4 id="chatUserName">Chọn người dùng để chat</h4>
                            <span class="user-status" id="chatUserStatus"></span>
                        </div>
                    @else
                        @if(isset($chatWithUser))
                            @php
                                $targetAvatar = $chatWithUser->avatar ?? null;
                                if ($targetAvatar && $targetAvatar !== 'null') {
                                    if (Str::startsWith($targetAvatar, ['http://', 'https://'])) {
                                        $avatarUrl = $targetAvatar;
                                    } elseif (Str::startsWith($targetAvatar, 'storage/')) {
                                        $avatarUrl = '/' . $targetAvatar;
                                    } else {
                                        $avatarUrl = $targetAvatar;
                                    }
                                } else {
                                    $avatarUrl = '/images/default-avatar.png';
                                }
                            @endphp
                            <div class="user-avatar">
                                <img src="{{ $avatarUrl }}" alt="{{ $chatWithUser->full_name ?? $chatWithUser->name }}" onerror="this.src='/images/default-avatar.png'">
                            </div>
                            <div class="user-details">
                                <h4>{{ $chatWithUser->full_name ?? $chatWithUser->name ?? 'User' }}</h4>
                                <span class="user-status online">Online</span>
                            </div>
                        @else
                            <div class="user-avatar">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="user-details">
                                <h4>{{ $admin->full_name ?? $admin->name ?? 'Admin' }}</h4>
                                <span class="user-status online">Online</span>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="chat-actions">
                    <button class="btn-icon" id="clearChat" title="Xóa lịch sử chat">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="chat-messages" id="chatMessages">
                @if(auth()->user()->role !== 'admin' && auth()->user()->role !== 'organizer')
                    <div class="chat-welcome">
                        <i class="fas fa-comments"></i>
                        <h3>Chào mừng đến với Chat hỗ trợ!</h3>
                        <p>Gửi tin nhắn của bạn</p>
                    </div>
                @else
                    <div class="chat-welcome">
                        <i class="fas fa-users"></i>
                        <h3>Chọn người dùng để bắt đầu chat</h3>
                    </div>
                @endif
            </div>

            <!-- Message Input -->
            <div class="chat-input-container">
                <form id="messageForm" class="message-form">
                    <input type="hidden" id="receiverId" name="receiver_id" value="{{ isset($chatWithUser) ? $chatWithUser->id : ((auth()->user()->role !== 'admin' && auth()->user()->role !== 'organizer') ? ($admin->id ?? '') : '') }}">
                    <div class="input-wrapper">
                        <textarea 
                            id="messageInput" 
                            name="message" 
                            placeholder="Nhập tin nhắn..." 
                            rows="1"
                            maxlength="1000"
                            {{ auth()->user()->role === 'admin' ? 'disabled' : '' }}
                        ></textarea>
                        <button type="submit" class="btn-send" id="sendBtn" {{ auth()->user()->role === 'admin' ? 'disabled' : '' }}>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="char-count">
                        <span id="charCount">0</span>/1000
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data for JS -->
<div id="chatData" 
     data-user-id="{{ auth()->id() }}" 
     data-user-role="{{ auth()->user()->role }}"
     data-admin-id="{{ $admin->id ?? '' }}"
     data-chat-with-id="{{ isset($chatWithUser) ? $chatWithUser->id : '' }}"
     data-chat-with-name="{{ isset($chatWithUser) ? ($chatWithUser->full_name ?? $chatWithUser->name) : '' }}"
     data-chat-with-avatar="{{ isset($chatWithUser) && $chatWithUser->avatar ? (Str::startsWith($chatWithUser->avatar, ['http://', 'https://']) ? $chatWithUser->avatar : (Str::startsWith($chatWithUser->avatar, 'storage/') ? '/' . $chatWithUser->avatar : $chatWithUser->avatar)) : '/images/default-avatar.png' }}"
     style="display: none;">
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/chat.js') }}"></script>
@endsection

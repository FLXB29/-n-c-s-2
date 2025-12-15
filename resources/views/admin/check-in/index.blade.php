@extends('layouts.admin')

@section('title', 'Quét QR Check-in')

@section('content')
<style>
    .check-in-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }

    .page-header h1 i {
        color: #6c5ce7;
    }

    .event-selector select {
        padding: 0.6rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 0.95rem;
        min-width: 280px;
        background: white;
        cursor: pointer;
    }

    .event-selector select:focus {
        outline: none;
        border-color: #6c5ce7;
    }

    .main-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .main-content {
            grid-template-columns: 1fr;
        }
    }

    .scan-section, .result-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .section-header {
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-header.success {
        background: linear-gradient(135deg, #00b894 0%, #55efc4 100%);
    }

    .section-header.error {
        background: linear-gradient(135deg, #d63031 0%, #ff7675 100%);
    }

    .section-header.warning {
        background: linear-gradient(135deg, #fdcb6e 0%, #ffeaa7 100%);
        color: #2d3436;
    }

    .section-header h2 {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .section-body {
        padding: 1.5rem;
    }

    /* Scanner Area */
    .scanner-area {
        text-align: center;
    }

    #qr-reader {
        width: 100%;
        max-width: 350px;
        min-height: 300px;
        margin: 0 auto 1.25rem;
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #qr-reader video {
        border-radius: 12px;
    }

    .scanner-controls {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .scanner-btn {
        padding: 0.65rem 1.25rem;
        border: none;
        border-radius: 10px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .scanner-btn.primary {
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        color: white;
    }

    .scanner-btn.secondary {
        background: #f1f2f6;
        color: #2d3436;
    }

    .scanner-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .scanner-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Manual Input */
    .manual-input {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 2px dashed #e0e0e0;
    }

    .manual-input h4 {
        margin-bottom: 0.75rem;
        color: #666;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .input-group {
        display: flex;
        gap: 0.5rem;
    }

    .input-group input {
        flex: 1;
        padding: 0.65rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 0.95rem;
        font-family: 'Courier New', monospace;
        text-transform: uppercase;
    }

    .input-group input:focus {
        outline: none;
        border-color: #6c5ce7;
    }

    /* Result Area */
    .result-placeholder {
        text-align: center;
        padding: 2.5rem;
        color: #999;
    }

    .result-placeholder i {
        font-size: 3.5rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .ticket-result {
        display: none;
    }

    .ticket-result.show {
        display: block;
    }

    .result-status {
        text-align: center;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border-radius: 12px;
    }

    .result-status.success {
        background: #d4edda;
        color: #155724;
    }

    .result-status.error {
        background: #f8d7da;
        color: #721c24;
    }

    .result-status.warning {
        background: #fff3cd;
        color: #856404;
    }

    .result-status i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .result-status h3 {
        margin: 0;
        font-size: 1.1rem;
    }

    /* Ticket Details */
    .ticket-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #666;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .detail-value {
        color: #1a1a2e;
        font-weight: 600;
        text-align: right;
        font-size: 0.9rem;
    }

    /* Confirm Button */
    .confirm-btn {
        width: 100%;
        padding: 0.85rem;
        background: linear-gradient(135deg, #00b894 0%, #55efc4 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .confirm-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,184,148,0.4);
    }

    .confirm-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Stats Panel */
    .stats-panel {
        margin-top: 1.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 1.5rem;
    }

    .stats-panel h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
    }

    .stat-item.highlight {
        background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        color: white;
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    /* Recent Check-ins */
    .recent-checkins h4 {
        font-size: 1rem;
        color: #1a1a2e;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkin-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .checkin-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }

    .checkin-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .checkin-code {
        font-family: monospace;
        font-weight: 600;
        color: #6c5ce7;
        font-size: 0.9rem;
    }

    .checkin-user {
        font-size: 0.8rem;
        color: #666;
    }

    .checkin-time {
        font-size: 0.8rem;
        color: #999;
    }

    /* Camera message */
    .camera-message {
        padding: 2rem;
        text-align: center;
        background: #fff3cd;
        border-radius: 12px;
        color: #856404;
    }

    .camera-message i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .camera-message p {
        margin: 0.5rem 0 0;
        font-size: 0.9rem;
    }

    /* Sound indicator */
    .sound-indicator {
        position: fixed;
        top: 80px;
        right: 1rem;
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-size: 0.9rem;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .sound-indicator.success {
        background: #d4edda;
        color: #155724;
    }

    .sound-indicator.error {
        background: #f8d7da;
        color: #721c24;
    }

    .sound-indicator.show {
        display: block;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>

<div class="check-in-container">
    <div class="page-header">
        <h1><i class="fas fa-qrcode"></i> Quét QR Check-in</h1>
        
        <div class="event-selector">
            <select id="eventSelect">
                <option value="">-- Tất cả sự kiện --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">
                        {{ $event->title }} - {{ $event->start_datetime->format('d/m/Y') }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="main-content">
        <!-- Scan Section -->
        <div class="scan-section">
            <div class="section-header">
                <i class="fas fa-camera"></i>
                <h2>Quét mã QR</h2>
            </div>
            <div class="section-body">
                <div class="scanner-area">
                    <div id="qr-reader">
                        <div class="camera-message">
                            <i class="fas fa-camera"></i>
                            <p>Nhấn "Bắt đầu quét" để mở camera</p>
                        </div>
                    </div>
                    
                    <div class="scanner-controls">
                        <button class="scanner-btn primary" id="startScanBtn" onclick="startScanner()">
                            <i class="fas fa-play"></i> Bắt đầu quét
                        </button>
                        <button class="scanner-btn secondary" id="stopScanBtn" onclick="stopScanner()" disabled>
                            <i class="fas fa-stop"></i> Dừng quét
                        </button>
                    </div>

                    <div class="manual-input">
                        <h4><i class="fas fa-keyboard"></i> Hoặc nhập mã vé thủ công:</h4>
                        <div class="input-group">
                            <input type="text" id="manualTicketCode" placeholder="VD: TKT-ABC123XYZ">
                            <button class="scanner-btn primary" onclick="manualScan()">
                                <i class="fas fa-search"></i> Kiểm tra
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Result Section -->
        <div class="result-section" id="resultSection">
            <div class="section-header" id="resultHeader">
                <i class="fas fa-ticket-alt"></i>
                <h2>Kết quả</h2>
            </div>
            <div class="section-body">
                <div class="result-placeholder" id="resultPlaceholder">
                    <i class="fas fa-qrcode"></i>
                    <p>Quét mã QR hoặc nhập mã vé để kiểm tra</p>
                </div>

                <div class="ticket-result" id="ticketResult">
                    <div class="result-status" id="resultStatus">
                        <i class="fas fa-check-circle"></i>
                        <h3 id="resultMessage">Vé hợp lệ!</h3>
                    </div>

                    <div class="ticket-details" id="ticketDetails">
                        <!-- Filled by JavaScript -->
                    </div>

                    <button class="confirm-btn" id="confirmBtn" onclick="confirmCheckIn()">
                        <i class="fas fa-check-double"></i> Xác nhận Check-in
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Panel -->
    <div class="stats-panel" id="statsPanel" style="display: none;">
        <h3><i class="fas fa-chart-bar"></i> Thống kê Check-in</h3>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" id="totalTickets">0</div>
                <div class="stat-label">Tổng số vé</div>
            </div>
            <div class="stat-item highlight">
                <div class="stat-number" id="checkedInCount">0</div>
                <div class="stat-label">Đã check-in</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="remainingCount">0</div>
                <div class="stat-label">Còn lại</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="checkinRate">0%</div>
                <div class="stat-label">Tỷ lệ</div>
            </div>
        </div>

        <div class="recent-checkins">
            <h4><i class="fas fa-history"></i> Check-in gần đây</h4>
            <div class="checkin-list" id="checkinList">
                <p style="text-align: center; color: #999;">Chưa có check-in nào</p>
            </div>
        </div>
    </div>
</div>

<!-- Sound indicator -->
<div class="sound-indicator" id="soundIndicator"></div>

<!-- Html5QrcodeScanner library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;
let currentTicketCode = null;
let selectedEventId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Check-in page loaded');
    console.log('Html5Qrcode available:', typeof Html5Qrcode !== 'undefined');
    
    document.getElementById('eventSelect').addEventListener('change', function() {
        selectedEventId = this.value;
        if (selectedEventId) {
            loadEventStats();
        } else {
            document.getElementById('statsPanel').style.display = 'none';
        }
    });

    // Enter key for manual input
    document.getElementById('manualTicketCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            manualScan();
        }
    });
});

function startScanner() {
    console.log('Starting scanner...');
    
    if (typeof Html5Qrcode === 'undefined') {
        showIndicator('Thư viện QR chưa sẵn sàng. Hãy tải lại trang.', 'error');
        return;
    }
    
    const qrReader = document.getElementById('qr-reader');
    qrReader.innerHTML = '<div style="padding: 2rem; text-align: center;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #6c5ce7;"></i><p style="margin-top: 1rem; color: #666;">Đang khởi động camera...</p></div>';
    
    html5QrCode = new Html5Qrcode("qr-reader");
    
    const config = {
        fps: 15,
        qrbox: { width: 200, height: 200 },
        aspectRatio: 1.0,
        formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ],
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        },
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true
    };
    
    html5QrCode.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).then(() => {
        console.log('Scanner started successfully');
        document.getElementById('startScanBtn').disabled = true;
        document.getElementById('stopScanBtn').disabled = false;
        showIndicator('Camera đã sẵn sàng! Hướng camera vào mã QR', 'success');
    }).catch(err => {
        console.error('Error starting scanner:', err);
        let errorMsg = 'Không thể truy cập camera!';
        
        if (err.name === 'NotAllowedError') {
            errorMsg = 'Bạn đã từ chối quyền truy cập camera. Hãy cho phép trong cài đặt trình duyệt.';
        } else if (err.name === 'NotFoundError') {
            errorMsg = 'Không tìm thấy camera trên thiết bị này.';
        } else if (err.name === 'NotReadableError') {
            errorMsg = 'Camera đang được sử dụng bởi ứng dụng khác.';
        }
        
        qrReader.innerHTML = `
            <div class="camera-message" style="background: #f8d7da; color: #721c24;">
                <i class="fas fa-exclamation-triangle"></i>
                <p><strong>${errorMsg}</strong></p>
                <p style="margin-top: 0.5rem;">Bạn vẫn có thể nhập mã vé thủ công bên dưới.</p>
            </div>
        `;
        showIndicator(errorMsg, 'error');
    });
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            document.getElementById('startScanBtn').disabled = false;
            document.getElementById('stopScanBtn').disabled = true;
            document.getElementById('qr-reader').innerHTML = `
                <div class="camera-message">
                    <i class="fas fa-camera"></i>
                    <p>Nhấn "Bắt đầu quét" để mở camera</p>
                </div>
            `;
        });
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log('QR Scanned!', decodedText, decodedResult);
    
    // Tạm dừng quét
    if (html5QrCode) {
        html5QrCode.pause();
    }
    
    // Phát âm thanh
    playBeep();
    
    // Hiển thị thông báo đã quét được
    showIndicator('Đã quét: ' + decodedText, 'success');
    
    // Kiểm tra vé
    checkTicket(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures - này là bình thường khi camera đang tìm QR
}

function manualScan() {
    const ticketCode = document.getElementById('manualTicketCode').value.trim().toUpperCase();
    if (!ticketCode) {
        showIndicator('Vui lòng nhập mã vé!', 'error');
        return;
    }
    checkTicket(ticketCode);
}

function checkTicket(ticketCode) {
    currentTicketCode = ticketCode;
    
    fetch('{{ route("admin.check-in.scan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ticket_code: ticketCode,
            event_id: selectedEventId
        })
    })
    .then(response => response.json())
    .then(data => {
        showResult(data);
        
        if (data.success) {
            playSound('success');
        } else {
            playSound('error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showIndicator('Lỗi kết nối!', 'error');
        resumeScanner();
    });
}

function showResult(data) {
    const resultPlaceholder = document.getElementById('resultPlaceholder');
    const ticketResult = document.getElementById('ticketResult');
    const resultHeader = document.getElementById('resultHeader');
    const resultStatus = document.getElementById('resultStatus');
    const ticketDetails = document.getElementById('ticketDetails');
    const confirmBtn = document.getElementById('confirmBtn');

    resultPlaceholder.style.display = 'none';
    ticketResult.classList.add('show');

    // Update header color
    resultHeader.className = 'section-header ' + data.type;

    // Update status
    resultStatus.className = 'result-status ' + data.type;
    
    let icon = 'fa-check-circle';
    if (data.type === 'error') icon = 'fa-times-circle';
    if (data.type === 'warning') icon = 'fa-exclamation-triangle';
    
    resultStatus.innerHTML = `
        <i class="fas ${icon}"></i>
        <h3>${data.message}</h3>
    `;

    // Update ticket details
    if (data.ticket) {
        ticketDetails.innerHTML = `
            <div class="detail-row">
                <span class="detail-label">Mã vé:</span>
                <span class="detail-value" style="color: #6c5ce7; font-family: monospace;">${data.ticket.ticket_code}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Sự kiện:</span>
                <span class="detail-value">${data.ticket.event_name}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ngày diễn ra:</span>
                <span class="detail-value">${data.ticket.event_date}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Loại vé:</span>
                <span class="detail-value">${data.ticket.ticket_type}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ghế:</span>
                <span class="detail-value">${data.ticket.seat}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Người mua:</span>
                <span class="detail-value">${data.ticket.user_name}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">${data.ticket.user_email}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Giá vé:</span>
                <span class="detail-value" style="color: #e74c3c; font-weight: 700;">${data.ticket.price_paid}</span>
            </div>
            ${data.check_in_time ? `
            <div class="detail-row">
                <span class="detail-label">Đã check-in lúc:</span>
                <span class="detail-value" style="color: #00b894; font-weight: 700;">${data.check_in_time}</span>
            </div>
            ` : ''}
        `;
        ticketDetails.style.display = 'block';
    } else {
        ticketDetails.style.display = 'none';
    }

    // Show/hide confirm button
    if (data.can_check_in) {
        confirmBtn.style.display = 'flex';
        confirmBtn.disabled = false;
    } else {
        confirmBtn.style.display = 'none';
    }
}

function confirmCheckIn() {
    if (!currentTicketCode) return;

    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

    fetch('{{ route("admin.check-in.confirm") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ticket_code: currentTicketCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showIndicator('✅ Check-in thành công!', 'success');
            playSound('success');
            
            // Update result
            showResult({
                success: true,
                type: 'success',
                message: 'Check-in thành công!',
                ticket: data.ticket,
                check_in_time: data.check_in_time,
                can_check_in: false
            });

            // Refresh stats if event selected
            if (selectedEventId) {
                loadEventStats();
            }
        } else {
            showIndicator(data.message, 'error');
            playSound('error');
        }

        // Resume scanner after delay
        setTimeout(resumeScanner, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        showIndicator('Lỗi kết nối!', 'error');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<i class="fas fa-check-double"></i> Xác nhận Check-in';
    });
}

function loadEventStats() {
    if (!selectedEventId) return;

    fetch(`/admin/check-in/event/${selectedEventId}/stats`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('statsPanel').style.display = 'block';
        document.getElementById('totalTickets').textContent = data.stats.total;
        document.getElementById('checkedInCount').textContent = data.stats.checked_in;
        document.getElementById('remainingCount').textContent = data.stats.total - data.stats.checked_in;
        
        const rate = data.stats.total > 0 ? Math.round((data.stats.checked_in / data.stats.total) * 100) : 0;
        document.getElementById('checkinRate').textContent = rate + '%';

        // Update recent check-ins
        const checkinList = document.getElementById('checkinList');
        if (data.tickets && data.tickets.length > 0) {
            checkinList.innerHTML = data.tickets.map(ticket => `
                <div class="checkin-item">
                    <div class="checkin-info">
                        <span class="checkin-code">${ticket.ticket_code}</span>
                        <span class="checkin-user">${ticket.user_name} - ${ticket.ticket_type}</span>
                    </div>
                    <span class="checkin-time">${ticket.check_in_time}</span>
                </div>
            `).join('');
        } else {
            checkinList.innerHTML = '<p style="text-align: center; color: #999;">Chưa có check-in nào</p>';
        }
    })
    .catch(error => {
        console.error('Error loading stats:', error);
    });
}

function resumeScanner() {
    if (html5QrCode) {
        try {
            const state = html5QrCode.getState();
            if (state === Html5QrcodeScannerState.PAUSED) {
                html5QrCode.resume();
            }
        } catch (e) {
            console.log('Scanner not in pausable state');
        }
    }
    
    // Reset UI for next scan
    currentTicketCode = null;
    document.getElementById('manualTicketCode').value = '';
}

function showIndicator(message, type) {
    const indicator = document.getElementById('soundIndicator');
    indicator.textContent = message;
    indicator.className = 'sound-indicator ' + type + ' show';
    
    setTimeout(() => {
        indicator.classList.remove('show');
    }, 3000);
}

function playBeep() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 1000;
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;
        
        oscillator.start();
        setTimeout(() => oscillator.stop(), 100);
    } catch (e) {
        console.log('Audio not supported');
    }
}

function playSound(type) {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        if (type === 'success') {
            oscillator.frequency.value = 800;
            setTimeout(() => { oscillator.frequency.value = 1000; }, 100);
            setTimeout(() => { oscillator.frequency.value = 1200; }, 200);
        } else {
            oscillator.frequency.value = 300;
        }
        
        oscillator.type = 'sine';
        gainNode.gain.value = 0.3;
        
        oscillator.start();
        setTimeout(() => oscillator.stop(), 300);
    } catch (e) {
        console.log('Audio not supported');
    }
}
</script>
@endsection

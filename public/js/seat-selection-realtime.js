/**
 * SEAT SELECTION REALTIME - Laravel Websockets
 * File này thay thế seat-selection.js với hỗ trợ realtime
 */

let selectedSeats = [];
let seatHoldTimers = {};
const HOLD_DURATION = 5 * 60 * 1000; // 5 phút

document.addEventListener('DOMContentLoaded', function() {
    initSeatSelection();
    initRealtimeConnection();
    
    // Lắng nghe sự kiện thay đổi Radio Button
    const radios = document.querySelectorAll('input[name="ticket_type_id"]');
    radios.forEach(r => {
        r.addEventListener('change', function() {
            if (selectedSeats.length === 0) {
                updateSeatVisuals();
            }
        });
    });

    // Release ghế khi người dùng rời trang
    window.addEventListener('beforeunload', function() {
        releaseAllSeats();
    });
});

// ===== REALTIME CONNECTION =====
function initRealtimeConnection() {
    if (typeof Echo === 'undefined' || !window.Echo) {
        console.log('Waiting for Echo to initialize...');
        setTimeout(initRealtimeConnection, 500);
        return;
    }

    const eventId = window.eventConfig.eventId;
    console.log(`Subscribing to channel: event.${eventId}.seats`);

    window.Echo.channel(`event.${eventId}.seats`)
        .listen('.seat.status.changed', (data) => {
            console.log('Seat status changed:', data);
            handleSeatStatusChange(data);
        });

    console.log('Realtime connection established!');
}

// Xử lý khi nhận được event từ server
function handleSeatStatusChange(data) {
    const seatElement = document.querySelector(`[data-db-id="${data.seat_id}"]`);
    if (!seatElement) return;

    const currentUserId = window.eventConfig.currentUserId;

    // Nếu là thay đổi từ chính mình thì bỏ qua
    if (data.held_by_user_id === currentUserId) return;

    // Cập nhật trạng thái ghế
    switch (data.status) {
        case 'held':
            seatElement.classList.remove('selected');
            seatElement.classList.add('held-by-other');
            seatElement.style.backgroundColor = '#ffa502';
            seatElement.style.color = '#fff';
            seatElement.title = 'Đang được người khác giữ';
            seatElement.onclick = null;
            
            // Xóa khỏi selectedSeats nếu đang chọn
            const idx = selectedSeats.findIndex(s => s.dbId == data.seat_id);
            if (idx > -1) {
                selectedSeats.splice(idx, 1);
                updateModalInfo();
            }
            break;

        case 'available':
            seatElement.classList.remove('held-by-other', 'sold');
            seatElement.style.backgroundColor = '';
            seatElement.style.color = '';
            seatElement.title = '';
            seatElement.onclick = () => toggleSeat(seatElement);
            break;

        case 'sold':
            seatElement.classList.remove('selected', 'held-by-other');
            seatElement.classList.add('sold');
            seatElement.style.backgroundColor = '#b2bec3';
            seatElement.style.color = '#fff';
            seatElement.title = 'Đã bán';
            seatElement.onclick = null;
            break;
    }

    updateSeatVisuals();
}

// ===== KHỞI TẠO SƠ ĐỒ GHẾ TỪ API =====
async function initSeatSelection() {
    const container = document.getElementById('seatsContainer');
    if (!container) return;

    container.innerHTML = '<div class="loading-spinner">Đang tải sơ đồ ghế...</div>';

    try {
        // Sử dụng API mới có thông tin realtime
        const response = await fetch(`/api/events/${window.eventConfig.eventId}/seats`);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'Lỗi tải ghế');
        }

        // Lưu current user id
        window.eventConfig.currentUserId = result.current_user_id;

        container.innerHTML = ''; 

        // Nhóm ghế theo hàng
        const seatsByRow = {};
        result.seats.forEach(seat => {
            const row = seat.row_number || 'X';
            if (!seatsByRow[row]) seatsByRow[row] = [];
            seatsByRow[row].push(seat);
        });

        if (Object.keys(seatsByRow).length === 0) {
            container.innerHTML = '<p>Chưa có sơ đồ ghế cho sự kiện này.</p>';
            return;
        }

        // Sắp xếp hàng
        const sortedRows = Object.keys(seatsByRow).sort();

        for (const rowNumber of sortedRows) {
            const seats = seatsByRow[rowNumber].sort((a, b) => {
                return parseInt(a.seat_number) - parseInt(b.seat_number);
            });

            const rowDiv = document.createElement('div');
            rowDiv.className = 'seat-row';
            
            const label = document.createElement('div');
            label.className = 'row-label';
            label.textContent = rowNumber;
            label.style.fontWeight = 'bold';
            label.style.marginRight = '10px';
            label.style.minWidth = '30px';
            rowDiv.appendChild(label);
            
            seats.forEach(seat => {
                const seatDiv = document.createElement('div');
                const seatId = `${seat.row_number}${seat.seat_number}`;
                
                seatDiv.className = `seat seat-type-${seat.ticket_type_id}`;
                seatDiv.textContent = seat.seat_number;
                
                seatDiv.dataset.id = seatId;
                seatDiv.dataset.dbId = seat.id;
                seatDiv.dataset.typeId = seat.ticket_type_id;
                
                const typeInfo = window.eventConfig.ticketTypes.find(t => t.id == seat.ticket_type_id);
                seatDiv.dataset.price = typeInfo ? typeInfo.price : 0;
                seatDiv.dataset.typeName = typeInfo ? typeInfo.name : '';

                // Xử lý trạng thái
                if (seat.status === 'sold') {
                    seatDiv.classList.add('sold');
                    seatDiv.style.backgroundColor = '#b2bec3';
                    seatDiv.style.color = '#fff';
                    seatDiv.title = 'Đã bán';
                } else if (seat.status === 'held') {
                    if (seat.held_by_user_id === result.current_user_id) {
                        // Ghế đang được chính mình giữ
                        seatDiv.classList.add('selected');
                        seatDiv.style.backgroundColor = '#6c5ce7';
                        seatDiv.style.color = '#fff';
                        seatDiv.title = 'Bạn đang giữ ghế này';
                        seatDiv.onclick = () => toggleSeat(seatDiv);
                        
                        // Thêm vào selectedSeats
                        selectedSeats.push({
                            id: seatId,
                            dbId: seat.id,
                            typeId: String(seat.ticket_type_id),
                            price: parseInt(seatDiv.dataset.price)
                        });
                    } else {
                        // Ghế đang được người khác giữ
                        seatDiv.classList.add('held-by-other');
                        seatDiv.style.backgroundColor = '#ffa502';
                        seatDiv.style.color = '#fff';
                        seatDiv.title = 'Đang được người khác giữ';
                    }
                } else {
                    // Available
                    seatDiv.onclick = () => toggleSeat(seatDiv);
                }

                rowDiv.appendChild(seatDiv);
            });
            
            container.appendChild(rowDiv);
        }
        
        updateSeatVisuals();
        updateModalInfo();

    } catch (error) {
        console.error('Lỗi tải ghế:', error);
        container.innerHTML = '<p style="color:red">Không thể tải dữ liệu ghế.</p>';
    }
}

// ===== XỬ LÝ CHỌN GHẾ =====
async function toggleSeat(seatElement) {
    const seatId = seatElement.dataset.id;
    const dbId = seatElement.dataset.dbId;
    const typeId = String(seatElement.dataset.typeId);
    const typeName = seatElement.dataset.typeName;
    const price = parseInt(seatElement.dataset.price);

    // Kiểm tra đã đăng nhập chưa
    if (!window.eventConfig.currentUserId) {
        alert('Vui lòng đăng nhập để chọn ghế!');
        window.location.href = '/login';
        return;
    }

    // Kiểm tra logic cùng loại vé
    if (selectedSeats.length > 0) {
        const firstSeatType = String(selectedSeats[0].typeId);
        if (firstSeatType !== typeId) {
            alert(`Bạn đang chọn ghế thuộc loại vé "${typeName}".\nVui lòng chỉ chọn ghế cùng loại vé trong một lần đặt!`);
            return;
        }
    }

    const existingIndex = selectedSeats.findIndex(s => s.id === seatId);

    if (existingIndex > -1) {
        // Bỏ chọn - Release ghế
        await releaseSeat(dbId);
        
        selectedSeats.splice(existingIndex, 1);
        seatElement.classList.remove('selected');
        seatElement.style.backgroundColor = ''; 
        seatElement.style.color = '';
        
        // Xóa timer countdown
        if (seatHoldTimers[dbId]) {
            clearTimeout(seatHoldTimers[dbId]);
            delete seatHoldTimers[dbId];
        }
    } else {
        // Chọn mới - Hold ghế
        if (selectedSeats.length >= 10) {
            alert('Bạn chỉ có thể chọn tối đa 10 ghế');
            return;
        }
        
        // Gọi API để hold ghế
        const holdResult = await holdSeat(dbId);
        
        if (holdResult.success) {
            selectedSeats.push({
                id: seatId,
                dbId: dbId,
                typeId: typeId,
                price: price
            });
            seatElement.classList.add('selected');
            seatElement.style.backgroundColor = '#6c5ce7';
            seatElement.style.color = '#fff';

            // Đặt timer để cảnh báo khi sắp hết hạn
            startHoldCountdown(dbId, holdResult.expires_at);
        } else {
            alert(holdResult.message || 'Không thể giữ ghế này');
        }
    }
    
    updateModalInfo();
    updateSeatVisuals();
}

// ===== API CALLS =====
async function holdSeat(seatDbId) {
    try {
        const response = await fetch(`/api/events/${window.eventConfig.eventId}/seats/hold`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ seat_id: seatDbId })
        });
        
        return await response.json();
    } catch (error) {
        console.error('Error holding seat:', error);
        return { success: false, message: 'Lỗi kết nối server' };
    }
}

async function releaseSeat(seatDbId) {
    try {
        const response = await fetch(`/api/events/${window.eventConfig.eventId}/seats/release`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ seat_id: seatDbId })
        });
        
        return await response.json();
    } catch (error) {
        console.error('Error releasing seat:', error);
        return { success: false, message: 'Lỗi kết nối server' };
    }
}

async function releaseAllSeats() {
    if (selectedSeats.length === 0) return;
    
    try {
        // Dùng sendBeacon để đảm bảo request được gửi khi rời trang
        const data = new FormData();
        data.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        
        navigator.sendBeacon(
            `/api/events/${window.eventConfig.eventId}/seats/release-all`,
            data
        );
    } catch (error) {
        console.error('Error releasing all seats:', error);
    }
}

// ===== COUNTDOWN TIMER =====
function startHoldCountdown(seatDbId, expiresAt) {
    const expiresTime = new Date(expiresAt).getTime();
    const warningTime = expiresTime - 60000; // Cảnh báo trước 1 phút
    
    // Cảnh báo trước khi hết hạn
    const warningTimer = setTimeout(() => {
        const seatInfo = selectedSeats.find(s => s.dbId == seatDbId);
        if (seatInfo) {
            showToast('Ghế ' + seatInfo.id + ' sẽ hết hạn trong 1 phút!', 'warning');
        }
    }, Math.max(0, warningTime - Date.now()));
    
    // Auto-release khi hết hạn
    const expireTimer = setTimeout(() => {
        const idx = selectedSeats.findIndex(s => s.dbId == seatDbId);
        if (idx > -1) {
            const seatElement = document.querySelector(`[data-db-id="${seatDbId}"]`);
            if (seatElement) {
                seatElement.classList.remove('selected');
                seatElement.style.backgroundColor = '';
                seatElement.style.color = '';
            }
            selectedSeats.splice(idx, 1);
            updateModalInfo();
            showToast('Ghế đã hết thời gian giữ!', 'error');
        }
    }, Math.max(0, expiresTime - Date.now()));
    
    seatHoldTimers[seatDbId] = { warning: warningTimer, expire: expireTimer };
}

// ===== TOAST NOTIFICATION =====
function showToast(message, type = 'info') {
    // Tạo toast container nếu chưa có
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 10000;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        background: ${type === 'error' ? '#e74c3c' : type === 'warning' ? '#f39c12' : '#27ae60'};
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease;
    `;
    toast.textContent = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}

// ===== CẬP NHẬT GIAO DIỆN =====
function updateSeatVisuals() {
    let activeTypeId = null;

    if (selectedSeats.length > 0) {
        activeTypeId = String(selectedSeats[0].typeId);
    } else {
        const checkedRadio = document.querySelector('input[name="ticket_type_id"]:checked');
        if (checkedRadio) {
            activeTypeId = String(checkedRadio.value);
        }
    }

    const allSeats = document.querySelectorAll('.seat');
    allSeats.forEach(seat => {
        if (seat.classList.contains('sold') || seat.classList.contains('held-by-other')) return;

        const seatTypeId = String(seat.dataset.typeId);

        if (activeTypeId && seatTypeId !== activeTypeId) {
            seat.classList.add('dimmed');
        } else {
            seat.classList.remove('dimmed');
        }
    });
}

function updateModalInfo() {
    const displayElement = document.getElementById('selectedSeatsDisplay');
    const modalTotalElement = document.getElementById('modalTotalPrice');
    
    if (!displayElement || !modalTotalElement) return;
    
    if (selectedSeats.length === 0) {
        displayElement.textContent = 'Chưa chọn ghế nào';
        modalTotalElement.textContent = '0 VNĐ';
    } else {
        const seatLabels = selectedSeats.map(s => s.id).join(', ');
        displayElement.textContent = seatLabels;
        
        const total = selectedSeats.reduce((sum, s) => sum + s.price, 0);
        modalTotalElement.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }
}

// ===== XÁC NHẬN CHỌN GHẾ =====
window.confirmSeats = function() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất một ghế');
        return;
    }
    
    const typeId = selectedSeats[0].typeId;
    const quantity = selectedSeats.length;

    const radio = document.querySelector(`input[name="ticket_type_id"][value="${typeId}"]`);
    if (radio) {
        radio.checked = true;
        radio.dispatchEvent(new Event('change')); 
    }

    const quantityInput = document.getElementById('ticketQuantity');
    if (quantityInput) {
        quantityInput.value = quantity;
    }

    const hiddenInput = document.getElementById('selectedSeatsInput');
    if (hiddenInput) {
        const seatDbIds = selectedSeats.map(s => s.dbId);
        hiddenInput.value = JSON.stringify(seatDbIds);
    }
    
    if (typeof window.updateTotal === 'function') {
        window.updateTotal();
    }
    
    closeModal('seatSelectionModal');
};

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

// CSS Animation
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .seat.held-by-other {
        background-color: #ffa502 !important;
        color: #fff !important;
        cursor: not-allowed !important;
    }
    .seat.dimmed {
        opacity: 0.3;
        cursor: not-allowed;
    }
`;
document.head.appendChild(style);

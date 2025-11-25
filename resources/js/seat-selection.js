// ===== SEAT SELECTION JAVASCRIPT (REAL DATABASE VERSION) =====

let selectedSeats = [];
let selectedTicketType = null;
let ticketQuantity = 1;

document.addEventListener('DOMContentLoaded', function() {
    initSeatSelection();
    // Các hàm init khác giữ nguyên nếu cần
});

// ===== 1. KHỞI TẠO SƠ ĐỒ GHẾ TỪ API =====
async function initSeatSelection() {
    const container = document.getElementById('seatsContainer');
    if (!container) return;

    container.innerHTML = '<div class="loading-spinner">Đang tải sơ đồ ghế...</div>';

    try {
        // Gọi API mà chúng ta đã khai báo trong Controller
        const response = await fetch(window.eventConfig.apiSeatsUrl);
        const seatsByRow = await response.json();

        container.innerHTML = ''; // Xóa loading

        if (Object.keys(seatsByRow).length === 0) {
            container.innerHTML = '<p>Chưa có sơ đồ ghế cho sự kiện này.</p>';
            return;
        }

        // Duyệt qua từng hàng ghế (A, B, C...) từ Database
        for (const [rowNumber, seats] of Object.entries(seatsByRow)) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'seat-row';
            
            // Nhãn hàng ghế
            const label = document.createElement('div');
            label.className = 'row-label';
            label.textContent = rowNumber;
            label.style.fontWeight = 'bold';
            label.style.marginRight = '10px';
            rowDiv.appendChild(label);
            
            // Tạo từng ghế
            seats.forEach(seat => {
                const seatDiv = document.createElement('div');
                const seatId = `${seat.row_number}${seat.seat_number}`;
                
                // Class cơ bản
                seatDiv.className = `seat seat-type-${seat.ticket_type_id}`;
                seatDiv.textContent = seat.seat_number;
                
                // Lưu dữ liệu vào dataset để dùng sau này
                seatDiv.dataset.id = seatId;
                seatDiv.dataset.dbId = seat.id;
                seatDiv.dataset.typeId = seat.ticket_type_id;
                
                // Lấy giá tiền từ biến global ticketTypes
                const typeInfo = window.eventConfig.ticketTypes.find(t => t.id == seat.ticket_type_id);
                seatDiv.dataset.price = typeInfo ? typeInfo.price : 0;

                // Xử lý trạng thái ghế
                if (seat.status === 'sold' || seat.status === 'blocked') {
                    seatDiv.classList.add('sold');
                    seatDiv.title = 'Đã bán';
                } else if (seat.status === 'reserved') {
                    seatDiv.classList.add('sold'); // Coi như đã bán
                    seatDiv.title = 'Đang giữ chỗ';
                } else {
                    // Chỉ gắn sự kiện click nếu ghế còn trống
                    seatDiv.onclick = () => toggleSeat(seatDiv);
                }

                rowDiv.appendChild(seatDiv);
            });
            
            container.appendChild(rowDiv);
        }

    } catch (error) {
        console.error('Lỗi tải ghế:', error);
        container.innerHTML = '<p style="color:red">Không thể tải dữ liệu ghế.</p>';
    }
}

// ===== 2. XỬ LÝ CHỌN GHẾ =====
function toggleSeat(seatElement) {
    const seatId = seatElement.dataset.id;
    const typeId = seatElement.dataset.typeId;
    const price = parseInt(seatElement.dataset.price);

    // Kiểm tra: Nếu đã chọn ghế rồi thì ghế tiếp theo phải cùng loại vé
    if (selectedSeats.length > 0) {
        if (selectedSeats[0].typeId != typeId) {
            alert('Vui lòng chỉ chọn ghế cùng hạng vé trong một lần đặt!');
            return;
        }
    }

    // Kiểm tra xem ghế đã chọn chưa
    const existingIndex = selectedSeats.findIndex(s => s.id === seatId);

    if (existingIndex > -1) {
        // Bỏ chọn
        selectedSeats.splice(existingIndex, 1);
        seatElement.classList.remove('selected');
    } else {
        // Chọn mới
        if (selectedSeats.length >= 10) {
            alert('Bạn chỉ có thể chọn tối đa 10 ghế');
            return;
        }
        
        selectedSeats.push({
            id: seatId,
            dbId: seatElement.dataset.dbId,
            typeId: typeId,
            price: price
        });
        seatElement.classList.add('selected');
    }
    
    updateModalInfo();
}

// ===== 3. CẬP NHẬT THÔNG TIN TRONG MODAL =====
function updateModalInfo() {
    const displayElement = document.getElementById('selectedSeatsDisplay');
    const modalTotalElement = document.getElementById('modalTotalPrice');
    
    if (selectedSeats.length === 0) {
        displayElement.textContent = 'Chưa chọn ghế nào';
        modalTotalElement.textContent = '0 VNĐ';
    } else {
        // Hiển thị danh sách ghế (A1, A2...)
        const seatLabels = selectedSeats.map(s => s.id).join(', ');
        displayElement.textContent = seatLabels;
        displayElement.style.color = '#6c5ce7';
        displayElement.style.fontWeight = 'bold';

        // Tính tổng tiền
        const total = selectedSeats.reduce((sum, s) => sum + s.price, 0);
        modalTotalElement.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }
}

// ===== 4. XÁC NHẬN CHỌN GHẾ (ĐẨY RA FORM NGOÀI) =====
// Hàm này cần được gán vào nút "Xác nhận" trong Modal HTML: onclick="confirmSeats()"
window.confirmSeats = function() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất một ghế');
        return;
    }
    
    const typeId = selectedSeats[0].typeId;
    const quantity = selectedSeats.length;

    // 1. Tự động chọn Radio button loại vé tương ứng ở bên ngoài
    const radio = document.querySelector(`input[name="ticket_type_id"][value="${typeId}"]`);
    if (radio) {
        radio.checked = true;
        // Kích hoạt sự kiện change để cập nhật giá bên ngoài nếu cần
        radio.dispatchEvent(new Event('change')); 
    }

    // 2. Cập nhật số lượng
    const quantityInput = document.getElementById('ticketQuantity');
    if (quantityInput) {
        quantityInput.value = quantity;
    }
    
    // 3. Gọi hàm cập nhật tổng tiền bên ngoài (hàm này nằm trong show.blade.php)
    if (typeof window.updateTotal === 'function') {
        window.updateTotal();
    }
    
    // 4. Đóng modal
    closeModal('seatSelectionModal');
};

// Helper: Đóng modal
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}
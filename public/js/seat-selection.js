// ===== SEAT SELECTION JAVASCRIPT (REAL DATABASE VERSION) =====

let selectedSeats = [];
let selectedTicketType = null;
let ticketQuantity = 1;

document.addEventListener('DOMContentLoaded', function() {
    initSeatSelection();
    
    // L?ng nghe s? ki?n thay d?i Radio Button bên ngoài
    const radios = document.querySelectorAll('input[name="ticket_type_id"]');
    radios.forEach(r => {
        r.addEventListener('change', function() {
            // N?u chua ch?n gh? nào thì c?p nh?t giao di?n theo Radio v?a ch?n
            if (selectedSeats.length === 0) {
                updateSeatVisuals();
            }
        });
    });
});

// ===== 1. KH?I T?O SO Ð? GH? T? API =====
async function initSeatSelection() {
    const container = document.getElementById('seatsContainer');
    if (!container) return;

    container.innerHTML = '<div class="loading-spinner">Ðang t?i so d? gh?...</div>';

    try {
        const response = await fetch(window.eventConfig.apiSeatsUrl);
        const seatsByRow = await response.json();

        container.innerHTML = ''; 

        if (Object.keys(seatsByRow).length === 0) {
            container.innerHTML = '<p>Chua có so d? gh? cho s? ki?n này.</p>';
            return;
        }

        for (const [rowNumber, seats] of Object.entries(seatsByRow)) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'seat-row';
            
            const label = document.createElement('div');
            label.className = 'row-label';
            label.textContent = rowNumber;
            label.style.fontWeight = 'bold';
            label.style.marginRight = '10px';
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

                if (seat.status === 'sold' || seat.status === 'blocked') {
                    seatDiv.classList.add('sold');
                    seatDiv.title = 'Ðã bán';
                } else if (seat.status === 'reserved') {
                    seatDiv.classList.add('sold');
                    seatDiv.title = 'Ðang gi? ch?';
                } else {
                    seatDiv.onclick = () => toggleSeat(seatDiv);
                }

                rowDiv.appendChild(seatDiv);
            });
            
            container.appendChild(rowDiv);
        }
        
        // C?p nh?t giao di?n l?n d?u (theo Radio m?c d?nh)
        updateSeatVisuals();

    } catch (error) {
        console.error('L?i t?i gh?:', error);
        container.innerHTML = '<p style="color:red">Không th? t?i d? li?u gh?.</p>';
    }
}

// ===== 2. X? LÝ CH?N GH? =====
function toggleSeat(seatElement) {
    const seatId = seatElement.dataset.id;
    const typeId = String(seatElement.dataset.typeId);
    const typeName = seatElement.dataset.typeName;
    const price = parseInt(seatElement.dataset.price);

    // Ki?m tra logic cùng lo?i vé (D? phòng, dù UI dã ch?n)
    if (selectedSeats.length > 0) {
        const firstSeatType = String(selectedSeats[0].typeId);
        if (firstSeatType !== typeId) {
            alert(`B?n dang ch?n gh? thu?c lo?i vé "${typeName}".\nVui lòng ch? ch?n gh? cùng lo?i vé trong m?t l?n d?t!`);
            return;
        }
    }

    const existingIndex = selectedSeats.findIndex(s => s.id === seatId);

    if (existingIndex > -1) {
        // B? ch?n
        selectedSeats.splice(existingIndex, 1);
        seatElement.classList.remove('selected');
        seatElement.style.backgroundColor = ''; 
        seatElement.style.color = '';
    } else {
        // Ch?n m?i
        if (selectedSeats.length >= 10) {
            alert('B?n ch? có th? ch?n t?i da 10 gh?');
            return;
        }
        
        selectedSeats.push({
            id: seatId,
            dbId: seatElement.dataset.dbId,
            typeId: typeId,
            price: price
        });
        seatElement.classList.add('selected');
        seatElement.style.backgroundColor = '#6c5ce7';
        seatElement.style.color = '#fff';
    }
    
    updateModalInfo();
    updateSeatVisuals(); // C?p nh?t l?i tr?ng thái m?/rõ
}

// ===== 3. C?P NH?T GIAO DI?N (LÀM M? GH? KHÁC LO?I) =====
function updateSeatVisuals() {
    let activeTypeId = null;

    // Uu tiên 1: Lo?i vé c?a gh? dang ch?n
    if (selectedSeats.length > 0) {
        activeTypeId = String(selectedSeats[0].typeId);
    } 
    // Uu tiên 2: Lo?i vé dang ch?n ? Radio Button bên ngoài
    else {
        const checkedRadio = document.querySelector('input[name="ticket_type_id"]:checked');
        if (checkedRadio) {
            activeTypeId = String(checkedRadio.value);
        }
    }

    const allSeats = document.querySelectorAll('.seat');
    allSeats.forEach(seat => {
        // B? qua gh? dã bán
        if (seat.classList.contains('sold')) return;

        const seatTypeId = String(seat.dataset.typeId);

        if (activeTypeId && seatTypeId !== activeTypeId) {
            seat.classList.add('dimmed');
        } else {
            seat.classList.remove('dimmed');
        }
    });
}

// ===== 4. C?P NH?T THÔNG TIN TRONG MODAL =====
function updateModalInfo() {
    const displayElement = document.getElementById('selectedSeatsDisplay');
    const modalTotalElement = document.getElementById('modalTotalPrice');
    
    if (selectedSeats.length === 0) {
        displayElement.textContent = 'Chua ch?n gh? nào';
        modalTotalElement.textContent = '0 VNÐ';
    } else {
        const seatLabels = selectedSeats.map(s => s.id).join(', ');
        displayElement.textContent = seatLabels;
        
        const total = selectedSeats.reduce((sum, s) => sum + s.price, 0);
        modalTotalElement.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }
}

// ===== 5. XÁC NH?N CH?N GH? =====
window.confirmSeats = function() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng ch?n ít nh?t m?t gh?');
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
    
    if (typeof window.updateTotal === 'function') {
        window.updateTotal();
    }
    
    closeModal('seatSelectionModal');
};

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
}

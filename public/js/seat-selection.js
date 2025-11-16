// ===== SEAT SELECTION JAVASCRIPT =====

let selectedSeats = [];
let selectedTicketType = null;
let ticketQuantity = 1;
let ticketPrice = 0;

document.addEventListener('DOMContentLoaded', function() {
    initSeatSelection();
    initTicketSelection();
    initQuantityControl();
});

// ===== TICKET TYPE SELECTION =====
function initTicketSelection() {
    const ticketOptions = document.querySelectorAll('input[name="ticket-type"]');
    
    ticketOptions.forEach(option => {
        option.addEventListener('change', function() {
            selectedTicketType = this.value;
            ticketPrice = parseInt(this.dataset.price);
            updateTotal();
        });
    });
}

// ===== QUANTITY CONTROL =====
function initQuantityControl() {
    const quantityInput = document.getElementById('ticketQuantity');
    
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            ticketQuantity = parseInt(this.value) || 1;
            updateTotal();
        });
    }
}

function increaseQuantity() {
    const input = document.getElementById('ticketQuantity');
    let value = parseInt(input.value) || 1;
    if (value < parseInt(input.max)) {
        value++;
        input.value = value;
        ticketQuantity = value;
        updateTotal();
    }
}

function decreaseQuantity() {
    const input = document.getElementById('ticketQuantity');
    let value = parseInt(input.value) || 1;
    if (value > parseInt(input.min)) {
        value--;
        input.value = value;
        ticketQuantity = value;
        updateTotal();
    }
}

function updateTotal() {
    const totalElement = document.getElementById('totalPrice');
    const total = ticketPrice * ticketQuantity;
    
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }
}

// ===== SEAT SELECTION =====
function initSeatSelection() {
    generateSeats();
}

function generateSeats() {
    const container = document.getElementById('seatsContainer');
    if (!container) return;
    
    // Generate 10 rows (A-J) with 20 seats each
    const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    const seatsPerRow = 20;
    
    // Randomly mark some seats as sold (for demo)
    const soldSeats = generateRandomSoldSeats(rows.length, seatsPerRow);
    
    rows.forEach(row => {
        const rowDiv = document.createElement('div');
        rowDiv.className = 'seat-row';
        
        // Row label
        const label = document.createElement('div');
        label.className = 'row-label';
        label.textContent = row;
        rowDiv.appendChild(label);
        
        // Create seats
        for (let i = 1; i <= seatsPerRow; i++) {
            const seat = document.createElement('div');
            const seatId = `${row}${i}`;
            seat.className = 'seat';
            seat.dataset.seatId = seatId;
            seat.textContent = i;
            
            // Mark as sold if in soldSeats array
            if (soldSeats.includes(seatId)) {
                seat.classList.add('sold');
            } else {
                seat.addEventListener('click', function() {
                    toggleSeat(this);
                });
            }
            
            rowDiv.appendChild(seat);
        }
        
        container.appendChild(rowDiv);
    });
}

function generateRandomSoldSeats(rows, seatsPerRow) {
    const soldSeats = [];
    const rowLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    const numberOfSoldSeats = Math.floor(Math.random() * 50) + 30; // 30-80 sold seats
    
    for (let i = 0; i < numberOfSoldSeats; i++) {
        const randomRow = rowLabels[Math.floor(Math.random() * rows)];
        const randomSeat = Math.floor(Math.random() * seatsPerRow) + 1;
        const seatId = `${randomRow}${randomSeat}`;
        
        if (!soldSeats.includes(seatId)) {
            soldSeats.push(seatId);
        }
    }
    
    return soldSeats;
}

function toggleSeat(seatElement) {
    const seatId = seatElement.dataset.seatId;
    
    // Check if seat is already selected
    const index = selectedSeats.indexOf(seatId);
    
    if (index > -1) {
        // Deselect seat
        selectedSeats.splice(index, 1);
        seatElement.classList.remove('selected');
    } else {
        // Check if max seats reached (e.g., 10 seats max)
        if (selectedSeats.length >= 10) {
            showAlert('Bạn chỉ có thể chọn tối đa 10 ghế', 'warning');
            return;
        }
        
        // Select seat
        selectedSeats.push(seatId);
        seatElement.classList.add('selected');
    }
    
    updateSelectedSeatsDisplay();
}

function updateSelectedSeatsDisplay() {
    const displayElement = document.getElementById('selectedSeatsDisplay');
    const modalTotalElement = document.getElementById('modalTotalPrice');
    
    if (displayElement) {
        if (selectedSeats.length === 0) {
            displayElement.textContent = 'Chưa chọn ghế nào';
            displayElement.style.color = 'var(--text-light)';
        } else {
            displayElement.textContent = selectedSeats.join(', ');
            displayElement.style.color = 'var(--primary-color)';
        }
    }
    
    // Update modal total price
    if (modalTotalElement && ticketPrice > 0) {
        const total = ticketPrice * selectedSeats.length;
        modalTotalElement.textContent = formatCurrency(total);
    }
}

function confirmSeats() {
    if (selectedSeats.length === 0) {
        showAlert('Vui lòng chọn ít nhất một ghế', 'warning');
        return;
    }
    
    if (!selectedTicketType) {
        showAlert('Vui lòng chọn loại vé', 'warning');
        closeModal('seatSelectionModal');
        return;
    }
    
    // Update quantity to match selected seats
    const quantityInput = document.getElementById('ticketQuantity');
    if (quantityInput) {
        quantityInput.value = selectedSeats.length;
        ticketQuantity = selectedSeats.length;
    }
    
    updateTotal();
    
    showAlert(`Đã chọn ${selectedSeats.length} ghế: ${selectedSeats.join(', ')}`, 'success');
    closeModal('seatSelectionModal');
    
    // Here you would normally save the selected seats to proceed with checkout
    console.log('Selected seats:', selectedSeats);
    console.log('Ticket type:', selectedTicketType);
    console.log('Total price:', ticketPrice * selectedSeats.length);
}

// Reset seat selection when modal closes
function resetSeatSelection() {
    selectedSeats = [];
    const seats = document.querySelectorAll('.seat.selected');
    seats.forEach(seat => seat.classList.remove('selected'));
    updateSelectedSeatsDisplay();
}

// Override closeModal to reset selection
const originalCloseModal = window.closeModal;
window.closeModal = function(modalId) {
    if (modalId === 'seatSelectionModal') {
        // Don't reset if user confirmed
        // resetSeatSelection();
    }
    originalCloseModal(modalId);
};

// ===== SCROLL TO BOOKING =====
function scrollToBooking() {
    const bookingCard = document.querySelector('.booking-card');
    if (bookingCard) {
        bookingCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ===== SHARE FUNCTIONALITY =====
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const eventUrl = window.location.href;
        const eventTitle = document.querySelector('.event-title').textContent;
        
        if (this.classList.contains('facebook')) {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(eventUrl)}`, '_blank');
        } else if (this.classList.contains('twitter')) {
            window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(eventUrl)}&text=${encodeURIComponent(eventTitle)}`, '_blank');
        } else if (this.classList.contains('link')) {
            // Copy link to clipboard
            navigator.clipboard.writeText(eventUrl).then(() => {
                showAlert('Đã sao chép link!', 'success');
            }).catch(() => {
                showAlert('Không thể sao chép link', 'error');
            });
        } else if (this.classList.contains('instagram')) {
            showAlert('Instagram không hỗ trợ chia sẻ trực tiếp', 'info');
        }
    });
});

// ===== COMMENT ACTIONS =====
document.querySelectorAll('.comment-actions button').forEach(btn => {
    btn.addEventListener('click', function() {
        const action = this.textContent.trim();
        if (action.includes('Thích')) {
            // Toggle like
            const currentText = this.innerHTML;
            if (currentText.includes('Thích')) {
                const count = parseInt(currentText.match(/\d+/)[0]);
                this.innerHTML = currentText.replace(/\d+/, count + 1);
                this.style.color = 'var(--primary-color)';
            }
        } else if (action.includes('Trả lời')) {
            showAlert('Chức năng trả lời đang được phát triển', 'info');
        }
    });
});

// ===== COMMENT FORM =====
const commentForm = document.querySelector('.comment-form');
if (commentForm) {
    const textarea = commentForm.querySelector('textarea');
    const submitBtn = commentForm.querySelector('button');
    
    submitBtn.addEventListener('click', function() {
        const comment = textarea.value.trim();
        
        if (comment) {
            // Here you would normally send the comment to the server
            showAlert('Bình luận của bạn đã được gửi!', 'success');
            textarea.value = '';
            
            // Simulate adding comment to list
            console.log('New comment:', comment);
        } else {
            showAlert('Vui lòng nhập nội dung bình luận', 'warning');
        }
    });
}

// ===== LOAD MORE COMMENTS =====
const loadMoreBtn = document.querySelector('.comments-section .btn-outline.btn-full');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
        
        // Simulate loading
        setTimeout(() => {
            this.textContent = 'Xem thêm bình luận';
            showAlert('Đã tải thêm bình luận', 'success');
        }, 1000);
    });
}

// ===== STICKY BOOKING CARD ON SCROLL (Mobile) =====
window.addEventListener('scroll', function() {
    if (window.innerWidth <= 768) {
        const bookingCard = document.querySelector('.booking-card');
        if (bookingCard && window.scrollY > 500) {
            bookingCard.style.position = 'fixed';
            bookingCard.style.bottom = '0';
            bookingCard.style.left = '0';
            bookingCard.style.right = '0';
            bookingCard.style.zIndex = '100';
            bookingCard.style.borderRadius = '1rem 1rem 0 0';
            bookingCard.style.maxHeight = '60vh';
            bookingCard.style.overflowY = 'auto';
        } else if (bookingCard) {
            bookingCard.style.position = 'static';
        }
    }
});

// Export functions
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
window.confirmSeats = confirmSeats;
window.scrollToBooking = scrollToBooking;

@extends('layouts.app')

@section('title', 'Thiết lập Vé & Ghế ngồi')

@section('content')
<link rel="stylesheet" href="{{ asset('css/event-detail.css') }}">
<style>
    /* Custom overrides for the setup page */
    .seat-map-preview-container {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        min-height: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .stage {
        width: 80%;
        margin-bottom: 40px;
    }
    .seat {
        cursor: default !important; /* Disable pointer in preview */
    }
    .seat:hover {
        transform: none !important; /* Disable hover effect in preview */
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Thiết lập Vé & Ghế ngồi: {{ $event->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('organizer.events.storeSetup', $event->id) }}" method="POST" id="setupForm">
                        @csrf
                        
                        <!-- Phần 1: Loại vé -->
                        <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-ticket-alt"></i> 1. Danh sách Loại vé</h5>
                        <div class="alert alert-info">
                            <small>Hãy tạo ít nhất một loại vé. Ví dụ: Vé thường, Vé VIP.</small>
                        </div>
                        
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered" id="ticketTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 35%">Tên loại vé</th>
                                        <th style="width: 25%">Giá vé (VNĐ)</th>
                                        <th style="width: 20%">Số lượng</th>
                                        <th style="width: 15%">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be added here -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-outline-primary" onclick="addTicketRow()">
                                <i class="fas fa-plus"></i> Thêm loại vé
                            </button>
                        </div>

                        <!-- Phần 2: Cấu hình ghế -->
                        <h5 class="mb-3 border-bottom pb-2 mt-5"><i class="fas fa-chair"></i> 2. Cấu hình Sơ đồ ghế & Xem trước</h5>
                        <div class="alert alert-warning">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="enableSeats" onchange="toggleSeats()">
                                <label class="form-check-label" for="enableSeats">
                                    <strong>Kích hoạt sơ đồ ghế ngồi</strong>
                                </label>
                            </div>
                        </div>

                        <div id="seatConfigSection" style="display: none;">
                            <div class="row">
                                <!-- Cột nhập liệu -->
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">Thêm vùng ghế</div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Loại vé áp dụng</label>
                                                <select id="zoneTicketSelect" class="form-select zone-ticket-select">
                                                    <!-- Options populated by JS -->
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tên khu vực (Section)</label>
                                                <input type="text" id="zoneName" class="form-control" placeholder="VD: Khán đài A">
                                                <small class="text-muted">Lưu vào cột Section trong DB</small>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Ký hiệu hàng</label>
                                                    <input type="text" id="rowLabel" class="form-control" placeholder="VD: A">
                                                    <small class="text-muted">Bắt đầu từ...</small>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label">Số lượng hàng</label>
                                                    <input type="number" id="rowCount" class="form-control" value="5" min="1">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Số ghế mỗi hàng</label>
                                                <input type="number" id="seatsPerRow" class="form-control" value="10" min="1">
                                            </div>
                                            <button type="button" class="btn btn-success w-100" onclick="addZoneConfig()">
                                                <i class="fas fa-plus"></i> Thêm vào sơ đồ
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cột hiển thị danh sách & Preview -->
                                <div class="col-md-8">
                                    <!-- Danh sách các vùng đã thêm -->
                                    <div class="table-responsive mb-3">
                                        <table class="table table-sm table-bordered" id="zoneTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Khu vực</th>
                                                    <th>Loại vé</th>
                                                    <th>Cấu hình</th>
                                                    <th>Tổng ghế</th>
                                                    <th>Xóa</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>

                                    <!-- Khu vực Preview -->
                                    <div class="card">
                                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                            <span><i class="fas fa-eye"></i> Xem trước sơ đồ (Preview)</span>
                                            <span class="badge bg-light text-dark">Màn hình chính ở phía này</span>
                                        </div>
                                        <div class="card-body bg-light" style="overflow-x: auto; min-height: 300px;">
                                            <div id="seatMapPreview" class="d-flex flex-column align-items-center gap-2">
                                                <p class="text-muted mt-5">Chưa có ghế nào được thêm.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg px-5">Hoàn tất thiết lập</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let ticketIndex = 0;
    let zoneIndex = 0;
    let zonesData = []; // Lưu trữ dữ liệu các vùng ghế để render preview

    document.addEventListener('DOMContentLoaded', function() {
        // Load existing data if available
        @if(isset($event->ticketTypes) && $event->ticketTypes->count() > 0)
            @foreach($event->ticketTypes as $ticket)
                addTicketRow('{{ $ticket->name }}', {{ $ticket->price }}, {{ $ticket->quantity }}, {{ $ticket->id }});
            @endforeach
        @else
            addTicketRow();
        @endif

        @if(isset($zones) && count($zones) > 0)
            // Enable seats checkbox
            document.getElementById('enableSeats').checked = true;
            toggleSeats();

            // Load zones
            const existingZones = @json($zones);
            existingZones.forEach(zone => {
                // We need to map the real ticket ID to the current ticketIndex key
                // Find the ticket row index that has the matching ticket ID (or name if ID not tracked well in UI yet)
                // In addTicketRow, we didn't store the ID in the DOM explicitly for lookup, but we can infer.
                // Actually, let's just use the ticket ID as the value if possible, but the UI uses index.
                // Let's find the index based on the ticket ID passed in addTicketRow.
                
                // Since we loaded tickets in order, the index should match the order.
                // But wait, ticketKey in zonesData refers to the row index (0, 1, 2...).
                // The $zones from controller has 'ticketKey' as the real DB ID.
                // We need to find which row index corresponds to that DB ID.
                
                // Simple hack: Loop through ticket rows and find the one with the matching ID?
                // Or just assume order is preserved.
                
                // Let's find the index.
                let targetIndex = -1;
                // We need to store the real ID in the ticket row to match.
                const ticketRows = document.querySelectorAll('#ticketTable tbody tr');
                ticketRows.forEach((row, index) => {
                    if (row.dataset.ticketId == zone.ticketKey) {
                        targetIndex = index;
                    }
                });

                if (targetIndex !== -1) {
                    // Add to zonesData
                    const newZone = {
                        id: zoneIndex,
                        ticketKey: targetIndex, // Use the UI index
                        ticketName: zone.ticketName,
                        name: zone.name,
                        rowLabel: zone.rowLabel,
                        rowCount: zone.rowCount,
                        seatsPerRow: zone.seatsPerRow,
                        totalSeats: zone.totalSeats
                    };
                    zonesData.push(newZone);
                    
                    // Render row in table
                    const table = document.getElementById('zoneTable').getElementsByTagName('tbody')[0];
                    const row = table.insertRow();
                    row.id = `zone-row-${zoneIndex}`;
                    row.innerHTML = `
                        <td>
                            <input type="hidden" name="zones[${zoneIndex}][ticket_key]" value="${targetIndex}">
                            <input type="hidden" name="zones[${zoneIndex}][name]" value="${newZone.name}">
                            <input type="hidden" name="zones[${zoneIndex}][row_label]" value="${newZone.rowLabel}">
                            <input type="hidden" name="zones[${zoneIndex}][row_count]" value="${newZone.rowCount}">
                            <input type="hidden" name="zones[${zoneIndex}][seats_per_row]" value="${newZone.seatsPerRow}">
                            <strong>${newZone.name}</strong>
                        </td>
                        <td>${newZone.ticketName}</td>
                        <td>${newZone.rowCount} hàng x ${newZone.seatsPerRow} ghế</td>
                        <td>${newZone.totalSeats}</td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeZoneConfig(${zoneIndex})">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    `;
                    zoneIndex++;
                }
            });
            renderPreview();
        @endif
    });

    // --- Ticket Functions (Giữ nguyên logic cũ nhưng thêm trigger update) ---
    function addTicketRow(name = '', price = '', quantity = '', id = null) {
        const table = document.getElementById('ticketTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow();
        row.id = `ticket-row-${ticketIndex}`;
        if (id) row.dataset.ticketId = id; // Store real ID for mapping
        
        row.innerHTML = `
            <td class="text-center align-middle fw-bold">${ticketIndex + 1}</td>
            <td>
                <input type="text" name="tickets[${ticketIndex}][name]" class="form-control ticket-name-input" placeholder="Ví dụ: Vé VIP" value="${name}" required oninput="updateZoneSelects(); renderPreview()">
            </td>
            <td>
                <input type="number" name="tickets[${ticketIndex}][price]" class="form-control" placeholder="0" min="0" value="${price}" required>
            </td>
            <td>
                <input type="number" name="tickets[${ticketIndex}][quantity]" class="form-control" placeholder="100" min="1" value="${quantity}" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeTicketRow(${ticketIndex})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        ticketIndex++;
        updateZoneSelects();
    }

    function removeTicketRow(index) {
        const row = document.getElementById(`ticket-row-${index}`);
        if (row) row.remove();
        updateZoneSelects();
        renderPreview(); // Re-render nếu xóa loại vé
    }

    function updateZoneSelects() {
        const select = document.getElementById('zoneTicketSelect');
        const currentVal = select.value;
        let options = '';
        
        for(let i = 0; i < ticketIndex; i++) {
            const ticketRow = document.getElementById(`ticket-row-${i}`);
            if(ticketRow) {
                const nameInput = ticketRow.querySelector(`input[name="tickets[${i}][name]"]`);
                const name = nameInput.value || `Loại vé #${i+1}`;
                options += `<option value="${i}">${name}</option>`;
            }
        }
        select.innerHTML = options;
        if(currentVal) select.value = currentVal;
    }

    function toggleSeats() {
        const section = document.getElementById('seatConfigSection');
        const checkbox = document.getElementById('enableSeats');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }

    // --- Zone & Preview Functions (Mới) ---

    function addZoneConfig() {
        // Lấy dữ liệu từ form nhập
        const ticketKey = document.getElementById('zoneTicketSelect').value;
        const zoneName = document.getElementById('zoneName').value || 'Khu vực ' + (zoneIndex + 1);
        const rowLabel = document.getElementById('rowLabel').value || 'A';
        const rowCount = parseInt(document.getElementById('rowCount').value) || 1;
        const seatsPerRow = parseInt(document.getElementById('seatsPerRow').value) || 10;

        // Lấy tên loại vé để hiển thị
        const ticketRow = document.getElementById(`ticket-row-${ticketKey}`);
        const ticketName = ticketRow ? ticketRow.querySelector('.ticket-name-input').value : 'N/A';

        // Thêm vào mảng dữ liệu
        const zone = {
            id: zoneIndex,
            ticketKey: ticketKey,
            ticketName: ticketName,
            name: zoneName,
            rowLabel: rowLabel,
            rowCount: rowCount,
            seatsPerRow: seatsPerRow,
            totalSeats: rowCount * seatsPerRow
        };
        zonesData.push(zone);

        // Render bảng danh sách
        const table = document.getElementById('zoneTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow();
        row.id = `zone-row-${zoneIndex}`;
        row.innerHTML = `
            <td>
                <input type="hidden" name="zones[${zoneIndex}][ticket_key]" value="${ticketKey}">
                <input type="hidden" name="zones[${zoneIndex}][name]" value="${zoneName}">
                <input type="hidden" name="zones[${zoneIndex}][row_label]" value="${rowLabel}">
                <input type="hidden" name="zones[${zoneIndex}][row_count]" value="${rowCount}">
                <input type="hidden" name="zones[${zoneIndex}][seats_per_row]" value="${seatsPerRow}">
                <strong>${zoneName}</strong>
            </td>
            <td>${ticketName}</td>
            <td>${rowCount} hàng x ${seatsPerRow} ghế</td>
            <td>${zone.totalSeats}</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeZoneConfig(${zoneIndex})">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

        zoneIndex++;
        renderPreview();
    }

    function removeZoneConfig(id) {
        // Xóa khỏi DOM
        const row = document.getElementById(`zone-row-${id}`);
        if(row) row.remove();

        // Xóa khỏi mảng dữ liệu
        zonesData = zonesData.filter(z => z.id !== id);
        renderPreview();
    }

    function renderPreview() {
        const container = document.getElementById('seatMapPreview');
        container.innerHTML = '';
        container.className = 'seat-map-preview-container'; // Add custom container class

        // Add Stage
        const stageDiv = document.createElement('div');
        stageDiv.className = 'stage';
        stageDiv.innerText = 'MÀN HÌNH / SÂN KHẤU';
        container.appendChild(stageDiv);

        if (zonesData.length === 0) {
            const msg = document.createElement('p');
            msg.className = 'text-muted mt-5';
            msg.innerText = 'Chưa có ghế nào được thêm.';
            container.appendChild(msg);
            return;
        }

        zonesData.forEach(zone => {
            // Zone Container
            const zoneWrapper = document.createElement('div');
            zoneWrapper.className = 'mb-4 w-100 d-flex flex-column align-items-center';
            
            // Zone Title
            const title = document.createElement('h5');
            title.className = 'mb-3 text-primary fw-bold';
            title.innerText = `${zone.name} - ${zone.ticketName}`;
            zoneWrapper.appendChild(title);

            // Seats Container for this zone (using event-detail.css class)
            const seatsContainer = document.createElement('div');
            seatsContainer.className = 'seats-container bg-transparent border-0 shadow-none p-0';
            seatsContainer.style.maxHeight = 'none'; 
            seatsContainer.style.overflow = 'visible';

            for (let r = 0; r < zone.rowCount; r++) {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'seat-row';
                
                // Row Label Logic
                let currentRowLabel = zone.rowLabel;
                // Check if input is a letter
                if (isNaN(zone.rowLabel)) {
                    // Increment character
                    currentRowLabel = String.fromCharCode(zone.rowLabel.charCodeAt(0) + r);
                } else {
                    // Increment number
                    currentRowLabel = parseInt(zone.rowLabel) + r;
                }

                const labelDiv = document.createElement('div');
                labelDiv.className = 'row-label';
                labelDiv.innerText = currentRowLabel;
                rowDiv.appendChild(labelDiv);

                // Seats
                for (let s = 1; s <= zone.seatsPerRow; s++) {
                    const seatDiv = document.createElement('div');
                    seatDiv.className = 'seat available'; 
                    seatDiv.innerText = s;
                    seatDiv.title = `${currentRowLabel}${s}`;
                    rowDiv.appendChild(seatDiv);
                }

                seatsContainer.appendChild(rowDiv);
            }

            zoneWrapper.appendChild(seatsContainer);
            container.appendChild(zoneWrapper);
        });
    }

    function getColorForTicket(key) {
        const colors = ['#4f46e5', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        return colors[key % colors.length];
    }
</script>
@endsection

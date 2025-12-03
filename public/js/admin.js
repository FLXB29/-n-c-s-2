// EventHub Admin Panel JavaScript

class AdminDashboard {
    constructor() {
        this.currentSection = 'overview';
        this.sidebarOpen = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadMockData();
        this.updateStats();
        this.setupCharts();
        this.loadSystemMetrics();
        this.loadLogs();
    }

    setupEventListeners() {
        // Sidebar navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const section = item.dataset.section;
                this.showSection(section);
            });
        });

        // Mobile sidebar toggle
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleFilter(e.target);
            });
        });

        // Search functionality
        const searchBoxes = document.querySelectorAll('.search-box');
        searchBoxes.forEach(box => {
            box.addEventListener('input', (e) => {
                this.handleSearch(e.target.value, e.target.dataset.table);
            });
        });

        // Action buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-icon')) {
                this.handleAction(e.target);
            }
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(form);
            });
        });
    }

    showSection(sectionName) {
        // Update navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');

        // Update content
        document.querySelectorAll('.section-content').forEach(section => {
            section.style.display = 'none';
        });
        document.getElementById(`${sectionName}-section`).style.display = 'block';

        this.currentSection = sectionName;
        
        // Load section-specific data
        this.loadSectionData(sectionName);
    }

    toggleSidebar() {
        const sidebar = document.querySelector('.admin-sidebar');
        this.sidebarOpen = !this.sidebarOpen;
        sidebar.classList.toggle('active', this.sidebarOpen);
    }

    handleFilter(filterBtn) {
        // Remove active class from all filter buttons in the same group
        const filterGroup = filterBtn.closest('.filter-group');
        filterGroup.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Add active class to clicked button
        filterBtn.classList.add('active');
        
        // Apply filter
        const filterValue = filterBtn.dataset.filter;
        const tableId = filterBtn.dataset.table;
        this.applyFilter(tableId, filterValue);
    }

    handleSearch(query, tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const matches = text.includes(query.toLowerCase());
            row.style.display = matches ? '' : 'none';
        });
    }

    applyFilter(tableId, filterValue) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            if (filterValue === 'all') {
                row.style.display = '';
            } else {
                const statusCell = row.querySelector('.status-badge');
                if (statusCell) {
                    const matches = statusCell.classList.contains(filterValue) || 
                                  statusCell.textContent.toLowerCase().includes(filterValue);
                    row.style.display = matches ? '' : 'none';
                }
            }
        });
    }

    handleAction(button) {
        const action = button.dataset.action;
        const itemId = button.dataset.id;
        
        switch (action) {
            case 'edit':
                this.editItem(itemId, button.dataset.type);
                break;
            case 'delete':
                this.deleteItem(itemId, button.dataset.type);
                break;
            case 'ban':
                this.banUser(itemId);
                break;
            case 'unban':
                this.unbanUser(itemId);
                break;
            case 'approve':
                this.approveEvent(itemId);
                break;
            case 'reject':
                this.rejectEvent(itemId);
                break;
            case 'view':
                this.viewDetails(itemId, button.dataset.type);
                break;
        }
    }

    loadMockData() {
        // Mock users data
        this.users = [
            { id: 1, name: 'Nguyễn Văn A', email: 'nguyenvana@email.com', role: 'User', status: 'active', joinDate: '2024-01-15', events: 5 },
            { id: 2, name: 'Trần Thị B', email: 'tranthib@email.com', role: 'Organizer', status: 'active', joinDate: '2024-02-20', events: 12 },
            { id: 3, name: 'Lê Văn C', email: 'levanc@email.com', role: 'User', status: 'inactive', joinDate: '2024-03-10', events: 2 },
            { id: 4, name: 'Phạm Thị D', email: 'phamthid@email.com', role: 'Organizer', status: 'pending', joinDate: '2024-03-25', events: 0 }
        ];

        // Mock events data
        this.events = [
            { id: 1, title: 'Tech Conference 2024', organizer: 'Trần Thị B', status: 'approved', date: '2024-04-15', attendees: 250, revenue: 15000000 },
            { id: 2, title: 'Music Festival', organizer: 'Nguyễn Văn E', status: 'pending', date: '2024-05-20', attendees: 0, revenue: 0 },
            { id: 3, title: 'Art Exhibition', organizer: 'Lê Thị F', status: 'rejected', date: '2024-04-30', attendees: 0, revenue: 0 },
            { id: 4, title: 'Food Fair', organizer: 'Phạm Văn G', status: 'approved', date: '2024-06-10', attendees: 180, revenue: 8500000 }
        ];

        // Mock organizers data
        this.organizers = [
            { id: 1, name: 'Trần Thị B', company: 'Tech Events Co.', events: 12, revenue: 45000000, rating: 4.8, status: 'verified' },
            { id: 2, name: 'Nguyễn Văn E', company: 'Music Productions', events: 8, revenue: 32000000, rating: 4.5, status: 'pending' },
            { id: 3, name: 'Lê Thị F', company: 'Art Gallery', events: 5, revenue: 18000000, rating: 4.2, status: 'verified' },
            { id: 4, name: 'Phạm Văn G', company: 'Food & Events', events: 15, revenue: 67000000, rating: 4.9, status: 'verified' }
        ];

        this.renderTables();
    }

    renderTables() {
        this.renderUsersTable();
        this.renderEventsTable();
        this.renderOrganizersTable();
    }

    renderUsersTable() {
        const tbody = document.querySelector('#users-table tbody');
        if (!tbody) return;

        tbody.innerHTML = this.users.map(user => `
            <tr>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${user.name.charAt(0)}</div>
                        <div>
                            <div class="user-name">${user.name}</div>
                            <div class="user-email">${user.email}</div>
                        </div>
                    </div>
                </td>
                <td><span class="role-badge ${user.role.toLowerCase()}">${user.role}</span></td>
                <td><span class="status-badge ${user.status}">${this.getStatusText(user.status)}</span></td>
                <td>${this.formatDate(user.joinDate)}</td>
                <td>${user.events}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon" data-action="view" data-id="${user.id}" data-type="user" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon" data-action="edit" data-id="${user.id}" data-type="user" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${user.status === 'active' ? 
                            `<button class="btn-icon danger" data-action="ban" data-id="${user.id}" title="Cấm">
                                <i class="fas fa-ban"></i>
                            </button>` :
                            `<button class="btn-icon success" data-action="unban" data-id="${user.id}" title="Bỏ cấm">
                                <i class="fas fa-check"></i>
                            </button>`
                        }
                        <button class="btn-icon danger" data-action="delete" data-id="${user.id}" data-type="user" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderEventsTable() {
        const tbody = document.querySelector('#events-table tbody');
        if (!tbody) return;

        tbody.innerHTML = this.events.map(event => `
            <tr>
                <td>
                    <div class="event-info">
                        <div class="event-title">${event.title}</div>
                        <div class="event-organizer">by ${event.organizer}</div>
                    </div>
                </td>
                <td>${this.formatDate(event.date)}</td>
                <td><span class="status-badge ${event.status}">${this.getStatusText(event.status)}</span></td>
                <td>${event.attendees.toLocaleString()}</td>
                <td>${this.formatCurrency(event.revenue)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon" data-action="view" data-id="${event.id}" data-type="event" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${event.status === 'pending' ? `
                            <button class="btn-icon success" data-action="approve" data-id="${event.id}" title="Phê duyệt">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn-icon danger" data-action="reject" data-id="${event.id}" title="Từ chối">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                        <button class="btn-icon" data-action="edit" data-id="${event.id}" data-type="event" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderOrganizersTable() {
        const tbody = document.querySelector('#organizers-table tbody');
        if (!tbody) return;

        tbody.innerHTML = this.organizers.map(organizer => `
            <tr>
                <td>
                    <div class="organizer-info">
                        <div class="organizer-name">${organizer.name}</div>
                        <div class="organizer-company">${organizer.company}</div>
                    </div>
                </td>
                <td>${organizer.events}</td>
                <td>${this.formatCurrency(organizer.revenue)}</td>
                <td>
                    <div class="rating">
                        <span class="rating-value">${organizer.rating}</span>
                        <div class="rating-stars">
                            ${this.renderStars(organizer.rating)}
                        </div>
                    </div>
                </td>
                <td><span class="status-badge ${organizer.status}">${this.getStatusText(organizer.status)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-icon" data-action="view" data-id="${organizer.id}" data-type="organizer" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-icon" data-action="edit" data-id="${organizer.id}" data-type="organizer" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${organizer.status === 'pending' ? `
                            <button class="btn-icon success" data-action="approve" data-id="${organizer.id}" title="Xác minh">
                                <i class="fas fa-check"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    updateStats() {
        // Update overview stats
        const stats = {
            totalUsers: this.users.length,
            activeUsers: this.users.filter(u => u.status === 'active').length,
            totalEvents: this.events.length,
            approvedEvents: this.events.filter(e => e.status === 'approved').length,
            totalRevenue: this.events.reduce((sum, e) => sum + e.revenue, 0),
            totalOrganizers: this.organizers.length
        };

        // Update stat cards
        document.querySelector('[data-stat="total-users"] .stat-value').textContent = stats.totalUsers;
        document.querySelector('[data-stat="active-users"] .stat-value').textContent = stats.activeUsers;
        document.querySelector('[data-stat="total-events"] .stat-value').textContent = stats.totalEvents;
        document.querySelector('[data-stat="total-revenue"] .stat-value').textContent = this.formatCurrency(stats.totalRevenue);
    }

    setupCharts() {
        // Placeholder for chart setup
        // In a real application, you would integrate with Chart.js or similar
        console.log('Charts would be initialized here');
    }

    loadSystemMetrics() {
        const metrics = [
            { label: 'CPU Usage', value: 45, max: 100 },
            { label: 'Memory', value: 68, max: 100 },
            { label: 'Storage', value: 32, max: 100 },
            { label: 'Network', value: 78, max: 100 }
        ];

        const container = document.querySelector('.system-metrics');
        if (container) {
            container.innerHTML = metrics.map(metric => `
                <div class="metric-item">
                    <div class="metric-label">${metric.label}</div>
                    <div class="metric-bar">
                        <div class="metric-fill" style="width: ${metric.value}%"></div>
                    </div>
                    <div class="metric-value">${metric.value}%</div>
                </div>
            `).join('');
        }
    }

    loadLogs() {
        const logs = [
            { time: '2024-03-28 14:30:25', level: 'info', message: 'User login: nguyenvana@email.com' },
            { time: '2024-03-28 14:28:15', level: 'warning', message: 'Failed login attempt from IP: 192.168.1.100' },
            { time: '2024-03-28 14:25:10', level: 'info', message: 'Event created: Tech Conference 2024' },
            { time: '2024-03-28 14:20:05', level: 'error', message: 'Payment processing failed for order #12345' },
            { time: '2024-03-28 14:15:30', level: 'info', message: 'System backup completed successfully' }
        ];

        const container = document.querySelector('.logs-container');
        if (container) {
            container.innerHTML = logs.map(log => `
                <div class="log-entry">
                    <div class="log-time">${log.time}</div>
                    <div class="log-level ${log.level}">${log.level.toUpperCase()}</div>
                    <div class="log-message">${log.message}</div>
                </div>
            `).join('');
        }
    }

    loadSectionData(section) {
        switch (section) {
            case 'users':
                this.renderUsersTable();
                break;
            case 'events':
                this.renderEventsTable();
                break;
            case 'organizers':
                this.renderOrganizersTable();
                break;
            case 'logs':
                this.loadLogs();
                break;
        }
    }

    // Action handlers
    editItem(id, type) {
        alert(`Chỉnh sửa ${type} với ID: ${id}`);
    }

    deleteItem(id, type) {
        if (confirm(`Bạn có chắc chắn muốn xóa ${type} này?`)) {
            // Remove from appropriate array
            switch (type) {
                case 'user':
                    this.users = this.users.filter(u => u.id != id);
                    this.renderUsersTable();
                    break;
                case 'event':
                    this.events = this.events.filter(e => e.id != id);
                    this.renderEventsTable();
                    break;
            }
            this.updateStats();
        }
    }

    banUser(id) {
        const user = this.users.find(u => u.id == id);
        if (user) {
            user.status = 'inactive';
            this.renderUsersTable();
            this.updateStats();
        }
    }

    unbanUser(id) {
        const user = this.users.find(u => u.id == id);
        if (user) {
            user.status = 'active';
            this.renderUsersTable();
            this.updateStats();
        }
    }

    approveEvent(id) {
        const event = this.events.find(e => e.id == id);
        if (event) {
            event.status = 'approved';
            this.renderEventsTable();
            this.updateStats();
        }
    }

    rejectEvent(id) {
        const event = this.events.find(e => e.id == id);
        if (event) {
            event.status = 'rejected';
            this.renderEventsTable();
            this.updateStats();
        }
    }

    viewDetails(id, type) {
        alert(`Xem chi tiết ${type} với ID: ${id}`);
    }

    handleFormSubmit(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        console.log('Form submitted:', data);
        alert('Form đã được gửi thành công!');
    }

    // Utility functions
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    getStatusText(status) {
        const statusMap = {
            'active': 'Hoạt động',
            'inactive': 'Không hoạt động',
            'pending': 'Chờ duyệt',
            'approved': 'Đã duyệt',
            'rejected': 'Từ chối',
            'verified': 'Đã xác minh'
        };
        return statusMap[status] || status;
    }

    renderStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        let stars = '';
        
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star"></i>';
        }
        
        if (hasHalfStar) {
            stars += '<i class="fas fa-star-half-alt"></i>';
        }
        
        const emptyStars = 5 - Math.ceil(rating);
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }
}

// Initialize admin dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard();
});

// Export for potential module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminDashboard;
}
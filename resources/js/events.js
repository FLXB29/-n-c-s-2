// ===== EVENTS PAGE JAVASCRIPT =====

document.addEventListener('DOMContentLoaded', function() {
    initFilters();
    initViewToggle();
    initSorting();
    initPriceRange();
    initMobileFilters();
});

// ===== FILTERS =====
function initFilters() {
    // Handle custom date range
    const dateRadios = document.querySelectorAll('input[name="date"]');
    const customDateRange = document.getElementById('customDateRange');
    
    dateRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'flex';
            } else {
                customDateRange.style.display = 'none';
            }
        });
    });
    
    // Handle filter changes
    const filterInputs = document.querySelectorAll('.filters-sidebar input');
    filterInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
    });
}

function applyFilters() {
    // Get selected categories
    const selectedCategories = [];
    document.querySelectorAll('input[name="category"]:checked').forEach(checkbox => {
        selectedCategories.push(checkbox.value);
    });
    
    // Get selected locations
    const selectedLocations = [];
    document.querySelectorAll('input[name="location"]:checked').forEach(checkbox => {
        selectedLocations.push(checkbox.value);
    });
    
    // Get selected date
    const selectedDate = document.querySelector('input[name="date"]:checked');
    const dateFilter = selectedDate ? selectedDate.value : null;
    
    // Get price range
    const priceRange = document.getElementById('priceRange');
    const maxPrice = priceRange ? priceRange.value : null;
    
    console.log('Applying filters:', {
        categories: selectedCategories,
        locations: selectedLocations,
        date: dateFilter,
        maxPrice: maxPrice
    });
    
    // Here you would normally fetch filtered data from API
    // For now, we'll just show a message
    showAlert('Đang áp dụng bộ lọc...', 'info');
    
    // Simulate filtering
    setTimeout(() => {
        const resultCount = Math.floor(Math.random() * 500) + 50;
        document.getElementById('resultCount').textContent = resultCount;
        showAlert('Đã áp dụng bộ lọc thành công!', 'success');
    }, 500);
}

function resetFilters() {
    // Reset all checkboxes and radios
    document.querySelectorAll('.filters-sidebar input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    document.querySelectorAll('.filters-sidebar input[type="radio"]').forEach(radio => {
        radio.checked = false;
    });
    
    // Reset price range
    const priceRange = document.getElementById('priceRange');
    if (priceRange) {
        priceRange.value = priceRange.max;
        updatePriceDisplay();
    }
    
    // Hide custom date range
    const customDateRange = document.getElementById('customDateRange');
    if (customDateRange) {
        customDateRange.style.display = 'none';
    }
    
    // Reset result count
    document.getElementById('resultCount').textContent = '486';
    
    showAlert('Đã đặt lại bộ lọc', 'info');
}

// ===== VIEW TOGGLE (Grid/List) =====
function initViewToggle() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const eventsGrid = document.getElementById('eventsGrid');
    
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Toggle grid/list view
            const view = this.dataset.view;
            if (view === 'list') {
                eventsGrid.classList.add('list-view');
            } else {
                eventsGrid.classList.remove('list-view');
            }
        });
    });
}

// ===== SORTING =====
function initSorting() {
    const sortSelect = document.getElementById('sortBy');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            console.log('Sorting by:', sortValue);
            
            showAlert('Đang sắp xếp...', 'info');
            
            // Here you would normally re-fetch data with new sort order
            // For now, just show a message
            setTimeout(() => {
                showAlert('Đã sắp xếp thành công!', 'success');
            }, 500);
        });
    }
}

// ===== PRICE RANGE =====
function initPriceRange() {
    const priceRange = document.getElementById('priceRange');
    
    if (priceRange) {
        priceRange.addEventListener('input', updatePriceDisplay);
        updatePriceDisplay(); // Initial update
    }
}

function updatePriceDisplay() {
    const priceRange = document.getElementById('priceRange');
    const priceMax = document.getElementById('priceMax');
    
    if (priceRange && priceMax) {
        const value = parseInt(priceRange.value);
        priceMax.textContent = formatCurrency(value);
    }
}

// ===== MOBILE FILTERS =====
function initMobileFilters() {
    // Create mobile filter toggle button if not exists
    if (window.innerWidth <= 768) {
        createMobileFilterToggle();
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth <= 768) {
                createMobileFilterToggle();
            } else {
                removeMobileFilterToggle();
            }
        }, 250);
    });
}

function createMobileFilterToggle() {
    // Check if button already exists
    if (document.querySelector('.mobile-filters-toggle')) return;
    
    // Create toggle button
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'mobile-filters-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-filter"></i>';
    toggleBtn.setAttribute('aria-label', 'Toggle Filters');
    
    // Create overlay
    const overlay = document.createElement('div');
    overlay.className = 'filters-overlay';
    
    document.body.appendChild(toggleBtn);
    document.body.appendChild(overlay);
    
    // Get sidebar
    const sidebar = document.querySelector('.filters-sidebar');
    
    // Toggle filters
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('mobile-active');
        overlay.classList.toggle('active');
        this.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('mobile-active') ? 'hidden' : 'auto';
    });
    
    // Close on overlay click
    overlay.addEventListener('click', function() {
        sidebar.classList.remove('mobile-active');
        overlay.classList.remove('active');
        toggleBtn.classList.remove('active');
        document.body.style.overflow = 'auto';
    });
}

function removeMobileFilterToggle() {
    const toggleBtn = document.querySelector('.mobile-filters-toggle');
    const overlay = document.querySelector('.filters-overlay');
    const sidebar = document.querySelector('.filters-sidebar');
    
    if (toggleBtn) toggleBtn.remove();
    if (overlay) overlay.remove();
    if (sidebar) {
        sidebar.classList.remove('mobile-active');
    }
    document.body.style.overflow = 'auto';
}

// ===== SEARCH FUNCTIONALITY =====
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchTerm = this.value.trim();
            if (searchTerm) {
                console.log('Searching for:', searchTerm);
                showAlert(`Đang tìm kiếm "${searchTerm}"...`, 'info');
                
                // Simulate search
                setTimeout(() => {
                    const resultCount = Math.floor(Math.random() * 100) + 10;
                    document.getElementById('resultCount').textContent = resultCount;
                    showAlert(`Tìm thấy ${resultCount} kết quả!`, 'success');
                }, 500);
            }
        }
    });
}

// ===== PAGINATION =====
document.querySelectorAll('.pagination a').forEach(link => {
    link.addEventListener('click', function(e) {
        if (!this.classList.contains('disabled')) {
            e.preventDefault();
            
            // Remove active class from all
            document.querySelectorAll('.pagination span, .pagination a').forEach(el => {
                el.classList.remove('active');
            });
            
            // Add active to clicked (if not an arrow)
            if (!this.innerHTML.includes('chevron')) {
                this.classList.add('active');
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            console.log('Loading page:', this.textContent);
        }
    });
});

// Make functions available globally
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;

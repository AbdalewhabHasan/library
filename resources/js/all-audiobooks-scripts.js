document.addEventListener('DOMContentLoaded', function() {
    // Initialize elements
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const languageFilter = document.getElementById('languageFilter');
    const clearFilters = document.getElementById('clearFilters');
    const sortBy = document.getElementById('sortBy');
    const gridView = document.getElementById('gridView');
    const tableView = document.getElementById('tableView');
    const gridContainer = document.getElementById('gridContainer');
    const tableContainer = document.getElementById('tableContainer');
    const visibleCount = document.getElementById('visibleCount');
    const noResults = document.getElementById('noResults');
    const searchForm = document.getElementById('searchForm');

    // Add loading animation to cards
    const cards = document.querySelectorAll('.fade-in-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // View toggle functionality
    if (gridView && tableView && gridContainer && tableContainer) {
        gridView.addEventListener('click', function() {
            gridContainer.classList.remove('d-none');
            tableContainer.classList.add('d-none');
            gridView.classList.add('active');
            tableView.classList.remove('active');
            
            // Re-trigger animations for grid cards
            cards.forEach((card, index) => {
                card.style.animation = 'none';
                setTimeout(() => {
                    card.style.animation = `fadeInUp 0.8s ease forwards`;
                    card.style.animationDelay = `${index * 0.05}s`;
                }, 10);
            });
        });

        tableView.addEventListener('click', function() {
            tableContainer.classList.remove('d-none');
            gridContainer.classList.add('d-none');
            tableView.classList.add('active');
            gridView.classList.remove('active');
        });
    }

    // Enhanced search and filter functionality
    function updateCounts() {
        const cards = document.querySelectorAll('.audio-book-card');
        const rows = document.querySelectorAll('.audio-book-row');
        
        let visibleCards = 0;
        let visibleRows = 0;

        cards.forEach(card => {
            if (!card.classList.contains('d-none')) visibleCards++;
        });

        rows.forEach(row => {
            if (!row.classList.contains('d-none')) visibleRows++;
        });

        const currentVisible = tableContainer && tableContainer.classList.contains('d-none') ? visibleCards : visibleRows;
        if (visibleCount) {
            visibleCount.textContent = currentVisible;
            
            // Add pulse effect when count changes
            visibleCount.parentElement.classList.add('pulse-effect');
            setTimeout(() => {
                visibleCount.parentElement.classList.remove('pulse-effect');
            }, 2000);
        }

        // Show/hide no results message
        if (currentVisible === 0) {
            if (noResults) noResults.classList.remove('d-none');
            if (gridContainer) gridContainer.classList.add('d-none');
            if (tableContainer) tableContainer.classList.add('d-none');
        } else {
            if (noResults) noResults.classList.add('d-none');
            if (gridView && gridView.classList.contains('active')) {
                if (gridContainer) gridContainer.classList.remove('d-none');
            } else {
                if (tableContainer) tableContainer.classList.remove('d-none');
            }
        }
    }

    function filterBooks() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value : '';
        const selectedLanguage = languageFilter ? languageFilter.value : '';

        const cards = document.querySelectorAll('.audio-book-card');
        const rows = document.querySelectorAll('.audio-book-row');

        // Filter cards with smooth animations
        cards.forEach((card, index) => {
            const title = card.dataset.title || '';
            const author = card.dataset.author || '';
            const category = card.dataset.category || '';
            const language = card.dataset.language || '';

            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesLanguage = !selectedLanguage || language === selectedLanguage;

            if (matchesSearch && matchesCategory && matchesLanguage) {
                card.classList.remove('d-none');
                card.style.animation = `fadeInUp 0.8s ease forwards`;
                card.style.animationDelay = `${index * 0.05}s`;
            } else {
                card.classList.add('d-none');
            }
        });

        // Filter table rows
        rows.forEach(row => {
            const title = row.dataset.title || '';
            const author = row.dataset.author || '';
            const category = row.dataset.category || '';
            const language = row.dataset.language || '';

            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            const matchesLanguage = !selectedLanguage || language === selectedLanguage;

            if (matchesSearch && matchesCategory && matchesLanguage) {
                row.classList.remove('d-none');
            } else {
                row.classList.add('d-none');
            }
        });

        updateCounts();
    }

    function sortBooks() {
        const sortValue = sortBy ? sortBy.value : 'title';
        const cards = Array.from(document.querySelectorAll('.audio-book-card'));
        const rows = Array.from(document.querySelectorAll('.audio-book-row'));

        // Sort cards
        cards.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortValue) {
                case 'title':
                    aValue = a.dataset.title || '';
                    bValue = b.dataset.title || '';
                    break;
                case 'author':
                    aValue = a.dataset.author || '';
                    bValue = b.dataset.author || '';
                    break;
                case 'duration':
                    aValue = parseInt(a.dataset.duration) || 0;
                    bValue = parseInt(b.dataset.duration) || 0;
                    break;
                case 'category':
                    aValue = a.dataset.category || '';
                    bValue = b.dataset.category || '';
                    break;
                default:
                    return 0;
            }

            if (typeof aValue === 'string') {
                return aValue.localeCompare(bValue, 'ar');
            } else {
                return aValue - bValue;
            }
        });

        // Sort rows
        rows.sort((a, b) => {
            let aValue, bValue;
            
            switch(sortValue) {
                case 'title':
                    aValue = a.dataset.title || '';
                    bValue = b.dataset.title || '';
                    break;
                case 'author':
                    aValue = a.dataset.author || '';
                    bValue = b.dataset.author || '';
                    break;
                case 'duration':
                    aValue = parseInt(a.dataset.duration) || 0;
                    bValue = parseInt(b.dataset.duration) || 0;
                    break;
                case 'category':
                    aValue = a.dataset.category || '';
                    bValue = b.dataset.category || '';
                    break;
                default:
                    return 0;
            }

            if (typeof aValue === 'string') {
                return aValue.localeCompare(bValue, 'ar');
            } else {
                return aValue - bValue;
            }
        });

        // Re-append with staggered animation
        const gridParent = gridContainer;
        const tableParent = tableContainer ? tableContainer.querySelector('tbody') : null;
        
        if (gridParent) {
            cards.forEach((card, index) => {
                gridParent.appendChild(card);
                card.style.animation = `fadeInUp 0.8s ease forwards`;
                card.style.animationDelay = `${index * 0.05}s`;
            });
        }
        
        if (tableParent) {
            rows.forEach(row => tableParent.appendChild(row));
        }
    }

    function clearAllFilters() {
        if (searchInput) searchInput.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (languageFilter) languageFilter.value = '';
        
        // Update URL to remove search parameter
        if (searchForm) {
            const url = new URL(window.location);
            url.searchParams.delete('search');
            window.history.pushState({}, '', url);
        }
        
        filterBooks();
        
        // Add visual feedback
        if (clearFilters) {
            const originalHTML = clearFilters.innerHTML;
            clearFilters.innerHTML = '<i class="fas fa-check"></i>';
            clearFilters.classList.add('btn-success');
            clearFilters.classList.remove('btn-outline-danger');
            
            setTimeout(() => {
                clearFilters.innerHTML = originalHTML;
                clearFilters.classList.remove('btn-success');
                clearFilters.classList.add('btn-outline-danger');
            }, 1500);
        }
    }

    // Event listeners
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterBooks, 300));
    }
    if (categoryFilter) categoryFilter.addEventListener('change', filterBooks);
    if (languageFilter) languageFilter.addEventListener('change', filterBooks);
    if (clearFilters) clearFilters.addEventListener('click', clearAllFilters);
    if (sortBy) sortBy.addEventListener('change', sortBooks);

    // Enhanced table sorting
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortType = this.dataset.sort;
            if (sortBy) sortBy.value = sortType;
            sortBooks();
            
            // Visual feedback
            document.querySelectorAll('.sortable i').forEach(icon => {
                icon.className = 'fas fa-sort ms-1';
            });
            const icon = this.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-sort-up ms-1';
                
                // Add temporary highlight
                this.style.background = 'rgba(255, 193, 7, 0.3)';
                setTimeout(() => {
                    this.style.background = '';
                }, 1000);
            }
        });
    });

    // Initialize counts
    updateCounts();

    // Add smooth scrolling behavior
    if (document.documentElement) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+F to focus search
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Escape to clear filters
        if (e.key === 'Escape') {
            clearAllFilters();
        }
        
        // Ctrl+G to toggle grid view
        if (e.ctrlKey && e.key === 'g') {
            e.preventDefault();
            if (gridView) gridView.click();
        }
        
        // Ctrl+T to toggle table view
        if (e.ctrlKey && e.key === 't') {
            e.preventDefault();
            if (tableView) tableView.click();
        }
    });

    // Add loading states for better UX
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري البحث...';
                submitBtn.disabled = true;
                
                // Re-enable after a short delay (in case of fast response)
                setTimeout(() => {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }, 2000);
            }
        });
    }

    // Add intersection observer for lazy loading animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-card');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all cards for lazy animation
    document.querySelectorAll('.premium-card').forEach(card => {
        observer.observe(card);
    });
});

// Enhanced audio preview functionality
function playPreview(audioUrl) {
    if (typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(document.getElementById('audioModal'));
        const audioSource = document.getElementById('audioSource');
        const audioPlayer = document.getElementById('audioPlayer');
        
        if (audioSource && audioPlayer) {
            audioSource.src = audioUrl;
            audioPlayer.load();
            modal.show();
            
            // Add visual feedback
            audioPlayer.addEventListener('loadstart', function() {
                const modalBody = document.querySelector('#audioModal .modal-body');
                if (modalBody) {
                    modalBody.style.opacity = '0.7';
                }
            });
            
            audioPlayer.addEventListener('canplay', function() {
                const modalBody = document.querySelector('#audioModal .modal-body');
                if (modalBody) {
                    modalBody.style.opacity = '1';
                }
            });
        }
    }
}

// Clean up audio when modal closes
const audioModal = document.getElementById('audioModal');
if (audioModal) {
    audioModal.addEventListener('hidden.bs.modal', function () {
        const audioPlayer = document.getElementById('audioPlayer');
        if (audioPlayer) {
            audioPlayer.pause();
            audioPlayer.currentTime = 0;
        }
    });
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add performance monitoring
if ('performance' in window) {
    window.addEventListener('load', function() {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData && perfData.loadEventEnd - perfData.loadEventStart > 3000) {
                console.log('Page load took longer than expected. Consider optimizing images and scripts.');
            }
        }, 0);
    });
}

// Add error handling for images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const placeholder = this.parentElement.querySelector('.gradient-placeholder');
            if (placeholder) {
                placeholder.style.display = 'flex';
            }
        });
    });
});


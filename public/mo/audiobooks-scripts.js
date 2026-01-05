// انتظر حتى يتم تحميل الصفحة بالكامل قبل تنفيذ أي كود
document.addEventListener('DOMContentLoaded', function () {

    // ========================================================
    //  1. الكود الأصلي للفلترة والبحث والتبديل بين العروض
    //  (هذا الكود سيعيد الصور والوظائف الأخرى)
    // ========================================================

    const gridViewBtn = document.getElementById('gridView');
    const tableViewBtn = document.getElementById('tableView');
    const gridContainer = document.getElementById('gridContainer');
    const tableContainer = document.getElementById('tableContainer');
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const languageFilter = document.getElementById('languageFilter');
    const clearFiltersBtn = document.getElementById('clearFilters');
    const noResults = document.getElementById('noResults');
    const visibleCountEl = document.getElementById('visibleCount');
    const audioBookCards = document.querySelectorAll('.audio-book-card, .audio-book-row');

    // وظيفة التبديل بين العرض الشبكي والجدولي
    if (gridViewBtn && tableViewBtn && gridContainer && tableContainer) {
        gridViewBtn.addEventListener('click', () => {
            gridContainer.classList.remove('d-none');
            tableContainer.classList.add('d-none');
            gridViewBtn.classList.add('active', 'btn-light');
            gridViewBtn.classList.remove('btn-outline-light');
            tableViewBtn.classList.remove('active', 'btn-light');
            tableViewBtn.classList.add('btn-outline-light');
        });

        tableViewBtn.addEventListener('click', () => {
            tableContainer.classList.remove('d-none');
            gridContainer.classList.add('d-none');
            tableViewBtn.classList.add('active', 'btn-light');
            tableViewBtn.classList.remove('btn-outline-light');
            gridViewBtn.classList.remove('active', 'btn-light');
            gridViewBtn.classList.add('btn-outline-light');
        });
    }

    // وظيفة الفلترة والبحث
    function filterAndSearch() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const category = categoryFilter ? categoryFilter.value : '';
        const language = languageFilter ? languageFilter.value : '';
        let visibleCount = 0;

        audioBookCards.forEach(card => {
            const title = card.dataset.title.toLowerCase();
            const author = card.dataset.author.toLowerCase();
            const cardCategory = card.dataset.category;
            const cardLanguage = card.dataset.language;

            const matchesSearch = title.includes(searchTerm) || author.includes(searchTerm);
            const matchesCategory = category === '' || cardCategory === category;
            const matchesLanguage = language === '' || cardLanguage === language;

            if (matchesSearch && matchesCategory && matchesLanguage) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCountEl) {
            visibleCountEl.textContent = visibleCount;
        }

        if (noResults) {
            noResults.classList.toggle('d-none', visibleCount > 0);
        }
    }

    if (searchInput) searchInput.addEventListener('keyup', filterAndSearch);
    if (categoryFilter) categoryFilter.addEventListener('change', filterAndSearch);
    if (languageFilter) languageFilter.addEventListener('change', filterAndSearch);

    // وظيفة مسح الفلاتر
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (categoryFilter) categoryFilter.value = '';
            if (languageFilter) languageFilter.value = '';
            filterAndSearch();
        });
    }


    // ========================================================
    //  2. الكود المضاف لتفعيل نافذة تأكيد الحذف
    //  (هذا الكود سيحافظ على عمل زر الحذف)
    // ========================================================
    
    // تعريف الدالة بشكل آمن على مستوى الـ window
    window.deleteAudioBook = function(id) {
        const deleteModalEl = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');

        if (deleteModalEl && deleteForm && typeof bootstrap !== 'undefined') {
            const deleteModal = new bootstrap.Modal(deleteModalEl);
            let actionUrl = `/publisher/audio-books/${id}`;
            deleteForm.setAttribute('action', actionUrl);
            deleteModal.show();
        } else {
            console.error('Delete modal elements or Bootstrap library not found!');
        }
    };

    // ========================================================
    //  3. الكود المضاف لتفعيل نافذة المعاينة الصوتية
    // ========================================================

    window.playPreview = function(url) {
        const audioModalEl = document.getElementById('audioModal');
        const audioPlayer = document.getElementById('audioPlayer');
        const audioSource = document.getElementById('audioSource');

        if (audioModalEl && audioPlayer && audioSource && typeof bootstrap !== 'undefined') {
            const audioModal = new bootstrap.Modal(audioModalEl);
            audioSource.src = url;
            audioPlayer.load();
            audioModal.show();
            audioPlayer.play();

            // إيقاف الصوت عند إغلاق النافذة
            audioModalEl.addEventListener('hidden.bs.modal', () => {
                audioPlayer.pause();
            }, { once: true }); // {once: true} لمنع تراكم المستمعين
        }
    };
});

/* Searchable Dropdown Script with Debounce */

// Utilitas Debounce (300ms by default)
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function initSearchableSelect(rowId) {
    const trigger = document.getElementById(`ss_trigger_${rowId}`);
    const dropdown = document.getElementById(`ss_dropdown_${rowId}`);
    const searchInput = document.getElementById(`ss_search_${rowId}`);
    const optionsContainer = document.getElementById(`ss_options_${rowId}`);
    const hiddenInput = document.getElementById(`ss_input_${rowId}`);

    if (!trigger || !dropdown || !searchInput || !optionsContainer || !hiddenInput) return;

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('show');
                const t = d.closest('.searchable-select').querySelector('.searchable-select-trigger');
                if(t) t.classList.remove('open');
            }
        });
        const isOpen = dropdown.classList.toggle('show');
        trigger.classList.toggle('open', isOpen);
        if (isOpen) {
            searchInput.value = '';
            filterOptions(rowId, '');
            setTimeout(() => searchInput.focus(), 50);
        }
    });

    // Implementasi debounce pada event input
    const debouncedFilter = debounce(function(query) {
        filterOptions(rowId, query);
    }, 300);

    searchInput.addEventListener('input', function(e) {
        e.stopPropagation();
        debouncedFilter(this.value);
    });

    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    optionsContainer.addEventListener('click', function(e) {
        const option = e.target.closest('.searchable-select-option');
        if (!option) return;
        e.stopPropagation();

        optionsContainer.querySelectorAll('.selected').forEach(o => o.classList.remove('selected'));
        option.classList.add('selected');

        hiddenInput.value = option.dataset.value;
        const triggerText = trigger.querySelector('.trigger-text');
        triggerText.textContent = option.dataset.label;
        triggerText.classList.remove('placeholder');

        dropdown.classList.remove('show');
        trigger.classList.remove('open');
        
        // Trigger event change (dibutuhkan jika ada hitung otomatis)
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

function filterOptions(rowId, query) {
    const optionsContainer = document.getElementById(`ss_options_${rowId}`);
    if (!optionsContainer) return;
    
    const options = optionsContainer.querySelectorAll('.searchable-select-option');
    const q = query.toLowerCase().trim();
    let visibleCount = 0;

    options.forEach(option => {
        const label = option.dataset.label.toLowerCase();
        const searchData = (option.dataset.search || '').toLowerCase();
        if (!q || label.includes(q) || searchData.includes(q)) {
            option.classList.remove('hidden');
            visibleCount++;
        } else {
            option.classList.add('hidden');
        }
    });

    let emptyMsg = optionsContainer.querySelector('.searchable-select-empty');
    if (visibleCount === 0) {
        if (!emptyMsg) {
            emptyMsg = document.createElement('div');
            emptyMsg.className = 'searchable-select-empty';
            emptyMsg.textContent = 'Data tidak ditemukan';
            optionsContainer.appendChild(emptyMsg);
        }
        emptyMsg.style.display = '';
    } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
    }
}

// Tutup dropdown saat klik di luar area
document.addEventListener('click', function() {
    document.querySelectorAll('.searchable-select-dropdown.show').forEach(d => {
        d.classList.remove('show');
        const trigger = d.closest('.searchable-select').querySelector('.searchable-select-trigger');
        if (trigger) trigger.classList.remove('open');
    });
});

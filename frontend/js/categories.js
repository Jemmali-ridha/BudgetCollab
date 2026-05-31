(function () {
    'use strict';

    const modal      = document.getElementById('categoryModal');
    const modalTitle = document.getElementById('modalCatTitle');
    const nameInput  = document.getElementById('catNameInput');
    const formAction = document.getElementById('catFormAction');
    const idInput    = document.getElementById('catIdInput');
    const closeBtn   = document.getElementById('closeCatModal');
    const cancelBtn  = document.getElementById('cancelCatModal');

    function openModal(title, name = '', id = '') {
        if (!modal) return;
        if (modalTitle) modalTitle.textContent = title;
        if (nameInput)  nameInput.value = name;
        if (idInput)    idInput.value   = id;
        if (formAction) {
            formAction.value = id ? 'update' : 'create';
        }
        modal.classList.add('active');
        setTimeout(() => nameInput && nameInput.focus(), 100);
    }

    function closeModal() {
        if (modal) modal.classList.remove('active');
    }

    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    if (modal) {
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    const addBtn = document.getElementById('addCategoryBtn');
    if (addBtn) addBtn.addEventListener('click', () => openModal('Add Category'));

    document.querySelectorAll('[data-edit-cat]').forEach(btn => {
        btn.addEventListener('click', () => {
            openModal('Edit Category', btn.dataset.name || '', btn.dataset.editCat || '');
        });
    });

    document.querySelectorAll('a[data-confirm]').forEach(link => {
        link.addEventListener('click', e => {
            if (!confirm(link.dataset.confirm || 'Delete this category?')) e.preventDefault();
        });
    });

    const ctx = document.getElementById('categoryChart');
    if (ctx && window.Chart) {
        // Récupère les données injectées par PHP via data attributes
        const labels = JSON.parse(ctx.dataset.labels || '[]');
        const values = JSON.parse(ctx.dataset.values || '[]');

        const green = getComputedStyle(document.documentElement)
            .getPropertyValue('--green').trim() || '#10B981';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: green,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' $' + ctx.parsed.x.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(128,128,128,0.1)' },
                        ticks: {
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('--text-secondary').trim(),
                            callback: v => '$' + v
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: getComputedStyle(document.documentElement)
                                .getPropertyValue('--text-secondary').trim()
                        }
                    }
                }
            }
        });
    }

})();
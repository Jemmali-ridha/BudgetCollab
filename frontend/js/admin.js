(function() {
    'use strict';

    document.querySelectorAll('.btn-delete, a[data-confirm]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const confirmMsg = btn.dataset.confirm || 'Are you sure? This action cannot be undone.';
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    });

    console.log('Admin JS loaded');
})();
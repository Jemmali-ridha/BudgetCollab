(function () {
    'use strict';

    document.querySelectorAll('.progress-bar__fill').forEach(bar => {
        const target = parseFloat(bar.dataset.width || bar.style.width) || 0;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = Math.min(target, 100) + '%';
        }, 80);
    });

    const declineBtns = document.querySelectorAll('[data-decline-invite]');
    declineBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const banner = btn.closest('.invitation-banner');
            if (banner) {
                banner.style.transition = 'opacity 0.3s ease, max-height 0.4s ease';
                banner.style.opacity = '0';
                banner.style.maxHeight = banner.offsetHeight + 'px';
                setTimeout(() => {
                    banner.style.maxHeight = '0';
                    banner.style.marginBottom = '0';
                    banner.style.padding = '0';
                    banner.style.border = 'none';
                }, 300);
            }
        });
    });

})();
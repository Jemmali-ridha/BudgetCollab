/**
 * budgets.js — BudgetCollab
 * Gestion de la page Budgets : modal, sélecteurs, suppression.
 */

(function () {
    'use strict';

    /* ---- Utilitaires ---- */
    const $ = (sel, ctx = document) => ctx.querySelector(sel);
    const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

    /* ---- Éléments ---- */
    const modal           = $('#budgetModal');
    const openModalBtns   = $$('[data-open-modal]');   // boutons "New Budget" et la carte "+"
    const closeModalBtn   = $('#closeModalBtn');
    const cancelModalBtn  = $('#cancelModalBtn');
    const selectorBtns    = $$('.selector-btn');
    const alertBtns       = $$('.alert-btn');
    const budgetTypeInput = $('#budgetTypeInput');
    const alertInput      = $('#alertThresholdInput');

    /* ---- Ouvrir / fermer le modal ---- */
    function openModal() {
        if (!modal) return;
        modal.classList.add('active');
        const firstInput = modal.querySelector('input[name="budget_name"]');
        if (firstInput) setTimeout(() => firstInput.focus(), 100);
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.remove('active');
    }

    openModalBtns.forEach(btn => btn.addEventListener('click', openModal));

    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (cancelModalBtn) cancelModalBtn.addEventListener('click', closeModal);

    // Fermer en cliquant sur l'overlay (hors container)
    if (modal) {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    }

    // Fermer avec Échap
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    /* ---- Sélecteur Individual / Shared ---- */
    selectorBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            selectorBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (budgetTypeInput) budgetTypeInput.value = btn.dataset.type;
        });
    });

    /* ---- Sélecteur Alert Threshold ---- */
    alertBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            alertBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (alertInput) alertInput.value = btn.dataset.threshold;
        });
    });

    /* ---- Barre de progression animée au chargement ---- */
    function animateBars() {
        $$('.progress-bar__fill').forEach(bar => {
            const target = parseFloat(bar.dataset.width || bar.style.width) || 0;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = Math.min(target, 100) + '%';
            }, 80);
        });
    }

    animateBars();

    /* ---- Confirmation de suppression ---- */
    $$('a[data-confirm]').forEach(link => {
        link.addEventListener('click', e => {
            if (!confirm(link.dataset.confirm || 'Confirm deletion?')) {
                e.preventDefault();
            }
        });
    });

})();
(function () {
    'use strict';

    /* ── Progress bars animation ── */
    document.querySelectorAll('.progress-bar__fill').forEach(bar => {
        const target = parseFloat(bar.dataset.width || bar.style.width) || 0;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = Math.min(target, 100) + '%';
        }, 80);
    });

    /* ── Confirmation de suppression ── */
    document.querySelectorAll('.btn-delete-card').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const confirmMsg = btn.dataset.confirm || 'Delete this shared budget? All data will be lost. This action cannot be undone.';
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        });
    });

    /* ── Decline invite banners with AJAX ── */
    document.querySelectorAll('[data-decline-invite]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const banner = btn.closest('.invitation-banner');
            const inviteId = btn.dataset.inviteId;

            if (!inviteId) {
                animateRemove(banner);
                return;
            }

            btn.disabled = true;
            try {
                const response = await fetch(
                    `index.php?page=shared-budgets&action=decline&id=${inviteId}`,
                    {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }
                );
                if (response.ok) {
                    animateRemove(banner);
                } else {
                    btn.disabled = false;
                }
            } catch (error) {
                console.error('Error declining invitation:', error);
                btn.disabled = false;
            }
        });
    });

    function animateRemove(el) {
        if (!el) return;
        el.style.transition = 'opacity 0.3s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease';
        el.style.maxHeight  = el.offsetHeight + 'px';
        el.style.overflow   = 'hidden';
        requestAnimationFrame(() => {
            el.style.opacity       = '0';
            el.style.maxHeight     = '0';
            el.style.marginBottom  = '0';
            el.style.paddingTop    = '0';
            el.style.paddingBottom = '0';
        });
        setTimeout(() => {
            el.remove();
        }, 400);
    }

    /* ── Invite modal ── */
    const modal           = document.getElementById('inviteModal');
    const modalClose      = document.getElementById('inviteModalClose');
    const modalCancel     = document.getElementById('inviteModalCancel');
    const modalBudgetName = document.getElementById('inviteModalBudgetName');
    const modalBudgetId   = document.getElementById('inviteModalBudgetId');
    const usersCheckboxList = document.getElementById('usersCheckboxList');
    const searchInput       = document.getElementById('usersSearchInput');
    const selectAllBtn      = document.getElementById('selectAllUsers');
    const deselectAllBtn    = document.getElementById('deselectAllUsers');

    function openModal(budgetId, budgetName) {
        if (!modal) return;

        modalBudgetId.value        = budgetId ?? '';
        modalBudgetName.textContent = budgetName ?? '';

        // Réinitialiser les cases à cocher
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
        
        // Réinitialiser la recherche
        if (searchInput) { 
            searchInput.value = ''; 
            filterUsers(''); 
        }

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        if (!modal) return;
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    function filterUsers(searchTerm) {
        if (!usersCheckboxList) return;
        const term = searchTerm.toLowerCase().trim();
        const items = usersCheckboxList.querySelectorAll('.user-checkbox-item');
        
        items.forEach(item => {
            const name  = item.querySelector('.user-checkbox-name')?.textContent.toLowerCase()  || '';
            const email = item.querySelector('.user-checkbox-email')?.textContent.toLowerCase() || '';
            const shouldShow = (term === '' || name.includes(term) || email.includes(term));
            item.style.display = shouldShow ? 'flex' : 'none';
        });
    }

    function selectAllVisible() {
        const visibleItems = usersCheckboxList?.querySelectorAll('.user-checkbox-item:not([style*="display: none"])');
        visibleItems?.forEach(item => {
            const checkbox = item.querySelector('.user-checkbox');
            if (checkbox) checkbox.checked = true;
        });
    }

    function deselectAll() {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
    }

    // Boutons invite dans les cartes
    document.querySelectorAll('.btn-invite-card').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const budgetId = btn.dataset.budgetId;
            const budgetName = btn.dataset.budgetName;
            if (budgetId && budgetName) {
                openModal(budgetId, budgetName);
            }
        });
    });

    // Fermeture du modal
    if (modalClose)      modalClose.addEventListener('click', closeModal);
    if (modalCancel)     modalCancel.addEventListener('click', closeModal);
    if (searchInput)     searchInput.addEventListener('input', e => filterUsers(e.target.value));
    if (selectAllBtn)    selectAllBtn.addEventListener('click', selectAllVisible);
    if (deselectAllBtn)  deselectAllBtn.addEventListener('click', deselectAll);

    // Fermer en cliquant sur le backdrop
    modal?.querySelector('.invite-modal__backdrop')?.addEventListener('click', closeModal);

    // Fermer avec Échap
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal && !modal.hidden) closeModal();
    });

    /* ── Soumission du formulaire d'invitation ── */
    const inviteForm = document.getElementById('inviteForm');
    if (inviteForm) {
        inviteForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (checkedBoxes.length === 0) {
                showModalError('Veuillez sélectionner au moins un utilisateur à inviter.');
                return;
            }

            const submitBtn = inviteForm.querySelector('[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi…';

            try {
                const response = await fetch(inviteForm.action, {
                    method: 'POST',
                    body: new FormData(inviteForm)
                });

                if (response.ok) {
                    closeModal();
                    window.location.reload();
                } else {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    showModalError('Une erreur est survenue lors de l\'envoi des invitations.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHTML;
                }
            } catch (error) {
                console.error('Error:', error);
                showModalError('Une erreur est survenue lors de l\'envoi des invitations.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            }
        });
    }

    function showModalError(msg) {
        let err = document.getElementById('modalErrorMsg');
        if (!err) {
            err = document.createElement('p');
            err.id = 'modalErrorMsg';
            err.className = 'modal-error-msg';
            const actionsDiv = inviteForm?.querySelector('.invite-modal__actions');
            if (actionsDiv) {
                actionsDiv.before(err);
            } else {
                inviteForm?.appendChild(err);
            }
        }
        err.textContent = msg;
        err.style.display = 'block';
        setTimeout(() => { 
            if (err) err.style.display = 'none'; 
        }, 4000);
    }

    // Debug: Afficher le nombre de boutons trouvés (à supprimer en production)
    console.log('shared_budgets.js chargé');
    console.log('Boutons .btn-invite-card trouvés:', document.querySelectorAll('.btn-invite-card').length);
    console.log('Boutons .btn-delete-card trouvés:', document.querySelectorAll('.btn-delete-card').length);
})();
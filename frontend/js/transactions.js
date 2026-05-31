function openModal(id) {
    document.getElementById(id).classList.add('active');
}
function closeTxModal(id) {
    document.getElementById(id).classList.remove('active');
}
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('active'); });
});

document.getElementById('addTxBtn').addEventListener('click', () => openModal('addTxModal'));

function openEditModal(tx) {
    document.getElementById('editTxId').value    = tx.id_transaction;
    document.getElementById('editDesc').value    = tx.description || '';
    document.getElementById('editAmount').value  = tx.montant;
    document.getElementById('editDate').value    = tx.date_transaction;
    document.getElementById('editCat').value     = tx.id_categorie;
    document.getElementById('editBudget').value  = tx.id_budget;
    document.getElementById('editTypeInput').value = tx.type_transaction;

    document.querySelectorAll('[data-type-val][data-form="edit"]').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.typeVal === tx.type_transaction);
    });

    openModal('editTxModal');
}

function confirmDelete(id, name) {
    document.getElementById('deleteTxName').textContent = name;
    document.getElementById('deleteTxLink').href =
        'index.php?page=transactions&action=delete&id=' + id;
    openModal('deleteTxModal');
}

document.querySelectorAll('[data-type-val]').forEach(btn => {
    btn.addEventListener('click', () => {
        const form = btn.dataset.form;
        document.querySelectorAll('[data-type-val][data-form="' + form + '"]')
                .forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(form + 'TypeInput').value = btn.dataset.typeVal;
    });
});

document.querySelector('.tx-search-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') e.target.closest('form').submit();
});

document.querySelectorAll('.tx-date-input').forEach(inp => {
    inp.addEventListener('change', () => inp.closest('form').submit());
});

function exportCSV() {
    const rows = [['Description','Category','Type','Amount','Date','Author']];
    document.querySelectorAll('.tx-row').forEach(row => {
        const cells = row.querySelectorAll('td');
        rows.push([
            cells[0]?.querySelector('.tx-desc')?.textContent?.trim() ?? '',
            cells[1]?.querySelector('.tx-badge')?.textContent?.trim() ?? '',
            cells[2]?.querySelector('.tx-badge')?.textContent?.trim() ?? '',
            cells[3]?.textContent?.trim() ?? '',
            cells[4]?.textContent?.trim() ?? '',
            cells[5]?.querySelector('span')?.textContent?.trim() ?? '',
        ]);
    });
    const csv  = rows.map(r => r.map(c => '"' + c.replace(/"/g,'""') + '"').join(',')).join('\n');
    const blob = new Blob([csv], { type: 'text/csv' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = 'transactions.csv';
    a.click();
}
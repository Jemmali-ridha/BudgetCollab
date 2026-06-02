<?php require_once __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="frontend/css/transactions.css">

<?php
$flash = $flash ?? getFlash();
$iconMap = [
    'shopping-cart'   => 'fas fa-shopping-cart',
    'car'             => 'fas fa-car',
    'home'            => 'fas fa-home',
    'heart'           => 'fas fa-heart',
    'film'            => 'fas fa-film',
    'book'            => 'fas fa-book',
    'tag'             => 'fas fa-tag',
    'more-horizontal' => 'fas fa-ellipsis-h',
    'gas-station'     => 'fas fa-gas-pump',
    'plane'           => 'fas fa-plane',
    'coffee'          => 'fas fa-coffee',
    'shopping-bag'    => 'fas fa-shopping-bag',
    'briefcase'       => 'fas fa-briefcase',
    'zap'             => 'fas fa-bolt',
    'wifi'            => 'fas fa-wifi',
    'dollar-sign'     => 'fas fa-dollar-sign',
];

function txIcon(string $icone, array $iconMap): string {
    return $iconMap[$icone] ?? 'fas fa-credit-card';
}

function avatarColor(string $name): string {
    $colors = ['#10B981','#6366F1','#F59E0B','#EF4444','#8B5CF6','#06B6D4','#F97316'];
    return $colors[crc32($name) % count($colors)];
}
?>

<?php if ($flash): ?>
<div class="flash-msg flash-<?= $flash['type'] ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= $flash['message'] ?>
    <button onclick="this.parentElement.remove()" class="flash-close">×</button>
</div>
<?php endif; ?>

<form method="GET" action="index.php" id="filterForm" class="tx-filters-bar">
    <input type="hidden" name="page" value="transactions">

    <div class="tx-search-wrap">
        <i class="fas fa-search tx-search-icon"></i>
        <input
            type="text"
            name="search"
            class="tx-search-input"
            placeholder="Search transactions..."
            value="<?= htmlspecialchars($filters['search']) ?>"
        >
    </div>

    <select name="category" class="tx-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id_categorie'] ?>"
                <?= $filters['category'] == $cat['id_categorie'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom_categorie']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="type" class="tx-select" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="depense"  <?= $filters['type'] === 'depense'  ? 'selected' : '' ?>>Expense</option>
        <option value="revenu"   <?= $filters['type'] === 'revenu'   ? 'selected' : '' ?>>Revenue</option>
    </select>

    <div class="tx-date-range">
        <i class="fas fa-calendar-alt tx-cal-icon"></i>
        <input type="date" name="date_from" class="tx-date-input" value="<?= htmlspecialchars($filters['date_from']) ?>" placeholder="From">
        <span class="tx-date-sep">→</span>
        <input type="date" name="date_to"   class="tx-date-input" value="<?= htmlspecialchars($filters['date_to'])   ?>" placeholder="To">
    </div>

    <button type="submit" class="tx-btn-icon" title="Apply filters">
        <i class="fas fa-filter"></i>
    </button>

    <?php if (array_filter($filters)): ?>
        <a href="index.php?page=transactions" class="tx-btn-icon tx-btn-clear" title="Clear filters">
            <i class="fas fa-times"></i>
        </a>
    <?php endif; ?>

    <div class="tx-bar-right">
        <button type="button" class="tx-btn-icon" title="Export CSV" onclick="exportCSV()">
            <i class="fas fa-download"></i>
        </button>
        <button type="button" class="btn-primary btn-sm" id="addTxBtn">
            <i class="fas fa-plus"></i>
            Add Transaction
        </button>
    </div>
</form>

<div class="tx-table-wrap section-card">
    <table class="tx-table">
        <thead>
            <tr>
                <th>Transaction</th>
                <th>Category</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Author</th>
                <th class="tx-th-actions">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($paginatedTx)): ?>
            <tr>
                <td colspan="7" class="tx-empty">
                    <div class="tx-empty-inner">
                        <i class="fas fa-receipt tx-empty-icon"></i>
                        <p>No transactions found</p>
                        <span>Try adjusting your filters or add a new transaction.</span>
                    </div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($paginatedTx as $tx): ?>
            <?php
                $isRevenu  = $tx['type_transaction'] === 'revenu';
                $amount    = number_format((float)$tx['montant'], 2);
                $fullName  = ($tx['prenom'] ?? '') . ' ' . ($tx['nom'] ?? '');
                $initials  = strtoupper(
                    substr($tx['prenom'] ?? '', 0, 1) .
                    substr($tx['nom']    ?? '', 0, 1)
                );
                $avatarBg  = avatarColor($fullName);
                $icon      = txIcon($tx['icone'] ?? '', $iconMap);
                $dateStr   = date('Y-m-d', strtotime($tx['date_transaction']));
            ?>
            <tr class="tx-row" data-id="<?= $tx['id_transaction'] ?>">
                <td class="tx-td-name">
                    <div class="tx-name-cell">
                        <div class="tx-icon-wrap" style="background:<?= htmlspecialchars($tx['couleur'] ?? '#607D8B') ?>22;">
                            <i class="<?= htmlspecialchars($icon) ?> tx-cat-icon"></i>
                        </div>
                        <span class="tx-desc"><?= htmlspecialchars($tx['description'] ?: 'No description') ?></span>
                    </div>
                </td>

                <td>
                    <span class="tx-badge tx-badge--cat"
                          style="background:<?= htmlspecialchars($tx['couleur'] ?? '#607D8B') ?>22;
                                 color:<?= htmlspecialchars($tx['couleur'] ?? '#607D8B') ?>;">
                        <?= htmlspecialchars($tx['nom_categorie']) ?>
                    </span>
                </td>

                <td>
                    <?php if ($isRevenu): ?>
                        <span class="tx-badge tx-badge--revenue">Revenue</span>
                    <?php else: ?>
                        <span class="tx-badge tx-badge--expense">Expense</span>
                    <?php endif; ?>
                </td>

                <td class="tx-amount <?= $isRevenu ? 'tx-amount--pos' : 'tx-amount--neg' ?>">
                    <?= $isRevenu ? '+' : '-' ?>$<?= $amount ?>
                </td>

                <td class="tx-date"><?= $dateStr ?></td>

                <td>
                    <div class="tx-author">
                        <div class="tx-avatar" style="background:<?= $avatarBg ?>;">
                            <?= htmlspecialchars($initials) ?>
                        </div>
                        <span><?= htmlspecialchars(trim($fullName)) ?></span>
                    </div>
                </td>

                <td class="tx-td-actions">
                    <button class="tx-action-btn tx-edit-btn"
                            title="Edit"
                            onclick="openEditModal(<?= htmlspecialchars(json_encode($tx)) ?>)">
                        <i class="fas fa-pen"></i>
                    </button>
                    <button class="tx-action-btn tx-delete-btn"
                            title="Delete"
                            onclick="confirmDelete(<?= $tx['id_transaction'] ?>, '<?= htmlspecialchars(addslashes($tx['description'] ?: 'this transaction')) ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="tx-pagination">
        <span class="tx-showing">
            Showing <?= $totalItems === 0 ? 0 : $offset + 1 ?> to <?= min($offset + $perPage, $totalItems) ?> of <?= $totalItems ?> transactions
        </span>
        <div class="tx-pages">
            <?php
            $baseParams = array_filter($filters);
            $baseParams['page'] = 'transactions';
            $queryBase = http_build_query($baseParams);
            ?>
            <a href="?<?= $queryBase ?>&p=<?= max(1, $currentPage-1) ?>"
               class="tx-page-btn <?= $currentPage <= 1 ? 'disabled' : '' ?>">Previous</a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= $queryBase ?>&p=<?= $i ?>"
                   class="tx-page-num <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="?<?= $queryBase ?>&p=<?= min($totalPages, $currentPage+1) ?>"
               class="tx-page-btn <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">Next</a>
        </div>
    </div>
</div>

<div class="modal-overlay" id="addTxModal">
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h2>Add Transaction</h2>
            <button class="modal-close" onclick="closeTxModal('addTxModal')">×</button>
        </div>
        <form class="modal-form" method="POST" action="index.php?page=transactions&action=create">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" placeholder="e.g., Grocery Store, Netflix..." required>
                </div>
                <div class="form-group">
                    <label>Amount ($)</label>
                    <input type="number" name="montant" placeholder="0.00" step="0.01" min="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Type</label>
                    <div class="selector-group">
                        <button type="button" class="selector-btn active" data-type-val="depense" data-form="add">Expense</button>
                        <button type="button" class="selector-btn" data-type-val="revenu" data-form="add">Revenue</button>
                    </div>
                    <input type="hidden" name="type_transaction" id="addTypeInput" value="depense">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date_transaction" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="id_categorie" class="tx-select tx-select--full" required>
                        <option value="">Select category...</option>
                        <?php
                        $system = array_filter($categories, fn($c) => $c['est_systeme'] ?? false);
                        $custom = array_filter($categories, fn($c) => !($c['est_systeme'] ?? false));
                        ?>
                        <?php if (!empty($system)): ?>
                            <optgroup label="General">
                                <?php foreach ($system as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>">
                                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php if (!empty($custom)): ?>
                            <optgroup label="My Categories">
                                <?php foreach ($custom as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>">
                                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Budget</label>
                    <select name="id_budget" class="tx-select tx-select--full" required>
                        <option value="">Select budget...</option>
                        <?php
                        $individual = array_filter($budgets, fn($b) => $b['budget_type'] === 'individual');
                        $shared     = array_filter($budgets, fn($b) => $b['budget_type'] === 'shared');
                        ?>
                        <?php if (!empty($individual)): ?>
                            <optgroup label="My Budgets">
                                <?php foreach ($individual as $b): ?>
                                    <option value="<?= $b['id_budget'] ?>">
                                        <?= htmlspecialchars($b['budget_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                        <?php if (!empty($shared)): ?>
                            <optgroup label="Shared Budgets">
                                <?php foreach ($shared as $b): ?>
                                    <option value="<?= $b['id_budget'] ?>">
                                        <?= htmlspecialchars($b['budget_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeTxModal('addTxModal')">Cancel</button>
                <button type="submit" class="btn-primary">Add Transaction</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editTxModal">
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h2>Edit Transaction</h2>
            <button class="modal-close" onclick="closeTxModal('editTxModal')">×</button>
        </div>
        <form class="modal-form" method="POST" action="index.php?page=transactions&action=update">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id_transaction" id="editTxId">

            <div class="form-row">
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" id="editDesc" placeholder="Description..." required>
                </div>
                <div class="form-group">
                    <label>Amount ($)</label>
                    <input type="number" name="montant" id="editAmount" placeholder="0.00" step="0.01" min="0.01" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Type</label>
                    <div class="selector-group">
                        <button type="button" class="selector-btn" data-type-val="depense" data-form="edit">Expense</button>
                        <button type="button" class="selector-btn" data-type-val="revenu" data-form="edit">Revenue</button>
                    </div>
                    <input type="hidden" name="type_transaction" id="editTypeInput" value="depense">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date_transaction" id="editDate" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <select name="id_categorie" id="editCat" class="tx-select tx-select--full" required>
                        <option value="">Select category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Budget</label>
                    <select name="id_budget" id="editBudget" class="tx-select tx-select--full" required>
                        <option value="">Select budget...</option>
                        <?php foreach ($budgets as $b): ?>
                            <option value="<?= $b['id_budget'] ?>"><?= htmlspecialchars($b['budget_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeTxModal('editTxModal')">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="deleteTxModal">
    <div class="modal-container modal-sm">
        <div class="modal-header">
            <h2>Delete Transaction</h2>
            <button class="modal-close" onclick="closeTxModal('deleteTxModal')">×</button>
        </div>
        <div class="tx-confirm-body">
            <div class="tx-confirm-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <p>Are you sure you want to delete <strong id="deleteTxName"></strong>?</p>
            <span>This action cannot be undone.</span>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeTxModal('deleteTxModal')">Cancel</button>
            <a href="#" id="deleteTxLink" class="btn-danger">Delete</a>
        </div>
    </div>
</div>



<script src="frontend/js/transactions.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
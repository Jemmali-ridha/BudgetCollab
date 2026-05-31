<?php
require_once __DIR__ . '/../includes/header.php';

function budgetSpent(array $budget): float
{
    return max(0, (float) ($budget['spent'] ?? 0));
}

function budgetStatus(float $pct): array
{
    if ($pct > 100) return ['over',     'Over Budget'];
    if ($pct >= 90) return ['warning',  'Warning'];
    if ($pct >= 50) return ['active',   'Active'];
    return ['on-track', 'On Track'];
}

function barColor(float $pct): string
{
    if ($pct > 100) return 'red';
    if ($pct >= 90) return 'amber';
    return 'green';
}
?>

    <?php if (!empty($flash)): ?>
        <div class="flash-message flash-message--<?= htmlspecialchars($flash['type']) ?>">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= $flash['message'] ?>
        </div>
    <?php endif; ?>

    <div class="budgets-grid">

        <?php if (empty($budgets)): ?>
            <div class="budgets-empty">
                <i class="fas fa-wallet"></i>
                <p>No budgets yet. Create your first one!</p>
            </div>
        <?php else: ?>
            <?php foreach ($budgets as $budget):
                $limit  = (float) ($budget['total_limit'] ?? 0);
                $spent  = budgetSpent($budget);
                $pct    = ($limit > 0) ? round(($spent / $limit) * 100, 1) : 0;
                [$statusKey, $statusLabel] = budgetStatus($pct);
                $color  = barColor($pct);
            ?>
            <div class="budget-card budget-card--<?= $statusKey ?>">

                <div class="budget-card__header">
                    <h3 class="budget-card__name">
                        <?= htmlspecialchars($budget['budget_name']) ?>
                    </h3>
                    <span class="status-badge status-badge--<?= $statusKey ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>

                <div>
                    <div class="budget-card__amount">
                        $<?= number_format($spent, 0, '.', ',') ?>
                    </div>
                    <?php if ($limit > 0): ?>
                        <div class="budget-card__limit">
                            of $<?= number_format($limit, 0, '.', ',') ?> limit
                        </div>
                    <?php else: ?>
                        <div class="budget-card__limit">No limit set</div>
                    <?php endif; ?>
                </div>

                <?php if ($limit > 0): ?>
                <div class="budget-card__progress-wrap">
                    <div class="progress-bar">
                        <div
                            class="progress-bar__fill progress-bar__fill--<?= $color ?>"
                            style="width: <?= min($pct, 100) ?>%"
                            data-width="<?= min($pct, 100) ?>"
                        ></div>
                    </div>
                    <span class="budget-card__percent"><?= $pct ?>% used</span>
                </div>
                <?php endif; ?>

                <div class="budget-card__footer">
                    <a
                        href="index.php?page=budgets&action=delete&id=<?= (int) $budget['id_budget'] ?>"
                        class="btn-icon btn-icon--danger"
                        data-confirm="Delete this budget? This action cannot be undone."
                        title="Delete"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>

            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="budget-card budget-card--new" data-open-modal tabindex="0"
             role="button" aria-label="Create a new budget">
            <div class="new-card__icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="new-card__title">Create New Budget</div>
            <p class="new-card__sub">Set up a new budget to track your spending</p>
        </div>

    </div>
    <div class="modal-overlay" id="budgetModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-container">

            <div class="modal-header">
                <h2 id="modalTitle">Create New Budget</h2>
                <button class="modal-close" id="closeModalBtn" aria-label="Close">&times;</button>
            </div>

            <form class="modal-form" method="POST" action="index.php?page=budgets&action=create">
                <input type="hidden" name="csrf_token"       value="<?= csrf_token() ?>">
                <input type="hidden" name="budget_type"      id="budgetTypeInput"     value="individual">
                <input type="hidden" name="alert_threshold"  id="alertThresholdInput" value="75%">

                <div class="form-group">
                    <label for="budgetName">Budget Name</label>
                    <input
                        type="text"
                        id="budgetName"
                        name="budget_name"
                        placeholder="e.g. Groceries, Transport…"
                        required
                        autocomplete="off"
                    >
                </div>

                <div class="form-group">
                    <label>Budget Type</label>
                    <div class="selector-group">
                        <button type="button" class="selector-btn active" data-type="individual">
                            <i class="fas fa-user" style="margin-right:6px; font-size:12px;"></i>Individual
                        </button>
                        <button type="button" class="selector-btn" data-type="shared">
                            <i class="fas fa-users" style="margin-right:6px; font-size:12px;"></i>Shared
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Period</label>
                    <div class="date-range">
                        <input type="date" name="start_date" required>
                        <span>→</span>
                        <input type="date" name="end_date"   required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="totalLimit">Budget Limit ($)</label>
                    <input
                        type="number"
                        id="totalLimit"
                        name="total_limit"
                        placeholder="0.00"
                        step="0.001"
                        min="0"
                    >
                </div>

                <div class="form-group">
                    <label>Alert Threshold</label>
                    <div class="alert-buttons">
                        <button type="button" class="alert-btn"        data-threshold="50%">50%</button>
                        <button type="button" class="alert-btn active" data-threshold="75%">75%</button>
                        <button type="button" class="alert-btn"        data-threshold="90%">90%</button>
                        <button type="button" class="alert-btn"        data-threshold="100%">100%</button>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plus"></i> Create Budget
                    </button>
                </div>

            </form>
        </div>
    </div>

<script src="frontend/js/budgets.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
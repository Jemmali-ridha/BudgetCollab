<?php
require_once __DIR__ . '/../includes/header.php';
 
?>

<style>
    .donut-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 16px 0;
}

.donut-wrapper canvas {
    max-width: 220px;
    max-height: 220px;
}
</style>
<!-- Stat Cards -->
<div class="stats-grid">
    <div class="stat-card stat-card--green">
        <div class="stat-card-top">
            <i class="fas fa-arrow-up"></i>
            <span class="stat-label">Total Revenues</span>
        </div>
        <div class="stat-value">$<?= number_format($stats['revenu'], 2) ?></div>
        <div class="stat-trend <?= str_starts_with($stats['trend_revenu'], '+') ? 'trend-up' : 'trend-down' ?>">
        </div>
    </div>
    <div class="stat-card stat-card--red">
        <div class="stat-card-top">
            <i class="fas fa-arrow-down"></i>
            <span class="stat-label">Total Expenses</span>
        </div>
        <div class="stat-value">$<?= number_format($stats['depense'], 2) ?></div>
        <div class="stat-trend <?= str_starts_with($stats['trend_depense'], '+') ? 'trend-down' : 'trend-up' ?>">
        </div>
    </div>
    <div class="stat-card stat-card--amber">
        <div class="stat-card-top">
            <i class="fas fa-scale-balanced"></i>
            <span class="stat-label">Balance</span>
        </div>
        <div class="stat-value">$<?= number_format($stats['balance'], 2) ?></div>
        <div class="stat-trend <?= str_starts_with($stats['trend_balance'], '+') ? 'trend-up' : 'trend-down' ?>">
        </div>
    </div>
    <div class="stat-card stat-card--indigo">
        <div class="stat-card-top">
            <i class="fas fa-percent"></i>
            <span class="stat-label">Budget Usage</span>
        </div>
        <div class="stat-value"><?= $stats['budget_usage'] ?>%</div>
    </div>
</div>

<!-- Two Columns -->
<div class="dashboard-two-columns">

    <!-- Budget Progress -->
    <div class="dashboard-col">
        <div class="section-card">
            <div class="section-header">
                <h2>Budget Progress</h2>
                <a href="index.php?page=budgets" class="view-all">View all →</a>
            </div>
            <div class="budget-progress-list">
                <?php if (empty($budgetProgress)): ?>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">No active budgets yet.</p>
                <?php else: ?>
                    <?php foreach ($budgetProgress as $b):
                        $limit = (float) $b['total_limit'];
                        $spent = (float) $b['spent'];
                        $pct   = $limit > 0 ? min(round(($spent / $limit) * 100, 1), 100) : 0;
                        $color = $pct >= 100 ? 'var(--red)' : ($pct >= 75 ? 'var(--amber)' : 'var(--green)');
                    ?>
                    <div class="budget-progress-item">
                        <div class="budget-progress-info">
                            <span class="budget-name"><?= htmlspecialchars($b['budget_name']) ?></span>
                            <span class="budget-amount" style="color: <?= $color ?>;">
                                $<?= number_format($spent, 0) ?> / $<?= number_format($limit, 0) ?>
                            </span>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: <?= $pct ?>%; background: <?= $color ?>;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Spending by Category -->
    <div class="dashboard-col">
        <div class="section-card">
            <div class="section-header">
                <h2>Spending by Category</h2>
                <a href="index.php?page=categories" class="view-all">Details →</a>
            </div>
            <?php if (empty($spendingByCategory)): ?>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">No spending data this month.</p>
            <?php else: ?>
                <div class="donut-wrapper">
                    <canvas id="spendingChart" aria-label="Spending by category donut chart"></canvas>
                </div>
                <div class="donut-legend">
                    <?php foreach ($spendingByCategory as $cat): ?>
                    <div class="donut-legend-item">
                        <span class="donut-legend-dot" style="background: <?= htmlspecialchars($cat['couleur']) ?>;"></span>
                        <span class="donut-legend-label"><?= htmlspecialchars($cat['nom_categorie']) ?></span>
                        <span class="donut-legend-pct"><?= $cat['pct'] ?>%</span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <script>
                const ctx = document.getElementById('spendingChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode(array_column($spendingByCategory, 'nom_categorie')) ?>,
                        datasets: [{
                            data:  <?= json_encode(array_column($spendingByCategory, 'pct')) ?>,
                            backgroundColor: <?= json_encode(array_column($spendingByCategory, 'couleur')) ?>,
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        cutout: '70%',
                        plugins: { legend: { display: false } },
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
                </script>
            <?php endif; ?>
        </div>
    </div>

</div>
        </main>
    </div>




        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
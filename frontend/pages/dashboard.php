<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BudgetCollab</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="frontend/css/style.css">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body class="dashboard-page">
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="logo-icon">✓</div>
                <span class="logo-text">Budget<span>Collab</span></span>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php?page=dashboard" class="nav-item active">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Transactions</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-wallet"></i>
                    <span>Budgets</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-users"></i>
                    <span>Shared Budgets</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Statistics</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="#" class="nav-item">
                    <i class="fas fa-user-shield"></i>
                    <span>Admin</span>
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar">
                    <span class="avatar-initials">JD</span>
                </div>
                <div class="user-info">
                    <span class="user-name">John Doe</span>
                    <span class="user-email">john@example.com</span>
                </div>
                <button class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="top-bar">
                <h1 class="page-title">Dashboard</h1>
                <div class="top-bar-actions">
                    <button class="notif-btn">
                        <i class="far fa-bell"></i>
                    </button>
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-sun"></i>
                        <div class="toggle-knob"></div>
                    </button>
                    <button class="btn-primary btn-sm" id="newBudgetBtn">
                        <i class="fas fa-plus"></i>
                        New Budget
                    </button>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card stat-card--green">
                    <div class="stat-card-top">
                        <i class="fas fa-arrow-up"></i>
                        <span class="stat-label">Total Revenues</span>
                    </div>
                    <div class="stat-value">$45,231</div>
                    <div class="stat-trend trend-up">+12.5% from last month</div>
                </div>
                <div class="stat-card stat-card--red">
                    <div class="stat-card-top">
                        <i class="fas fa-arrow-down"></i>
                        <span class="stat-label">Total Expenses</span>
                    </div>
                    <div class="stat-value">$32,450</div>
                    <div class="stat-trend trend-down">+8.2% from last month</div>
                </div>
                <div class="stat-card stat-card--amber">
                    <div class="stat-card-top">
                        <i class="fas fa-scale-balanced"></i>
                        <span class="stat-label">Balance</span>
                    </div>
                    <div class="stat-value">$12,781</div>
                    <div class="stat-trend trend-up">+4.3% from last month</div>
                </div>
                <div class="stat-card stat-card--indigo">
                    <div class="stat-card-top">
                        <i class="fas fa-percent"></i>
                        <span class="stat-label">Budget Usage</span>
                    </div>
                    <div class="stat-value">68%</div>
                    <div class="stat-trend">+2.1% from last month</div>
                </div>
            </div>

            <!-- Two Columns Layout -->
            <div class="dashboard-two-columns">

                <!-- Left: Budget Progress -->
                <div class="dashboard-col">
                    <div class="section-card">
                        <div class="section-header">
                            <h2>Budget Progress</h2>
                            <a href="#" class="view-all">View all →</a>
                        </div>
                        <div class="budget-progress-list">
                            <div class="budget-progress-item">
                                <div class="budget-progress-info">
                                    <span class="budget-name">Groceries</span>
                                    <span class="budget-amount" style="color: var(--green);">$450 / $600</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: 75%; background: var(--green);"></div>
                                </div>
                            </div>
                            <div class="budget-progress-item">
                                <div class="budget-progress-info">
                                    <span class="budget-name">Transport</span>
                                    <span class="budget-amount" style="color: var(--amber);">$280 / $400</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: 70%; background: var(--amber);"></div>
                                </div>
                            </div>
                            <div class="budget-progress-item">
                                <div class="budget-progress-info">
                                    <span class="budget-name">Entertainment</span>
                                    <span class="budget-amount" style="color: var(--red);">$520 / $500</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill progress-bar-fill--over" style="width: 100%; background: var(--red);"></div>
                                </div>
                            </div>
                            <div class="budget-progress-item">
                                <div class="budget-progress-info">
                                    <span class="budget-name">Shopping</span>
                                    <span class="budget-amount" style="color: var(--indigo);">$180 / $300</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: 60%; background: var(--indigo);"></div>
                                </div>
                            </div>
                            <div class="budget-progress-item">
                                <div class="budget-progress-info">
                                    <span class="budget-name">Bills</span>
                                    <span class="budget-amount" style="color: #8B5CF6;">$340 / $400</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: 85%; background: #8B5CF6;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Spending by Category (Donut Chart) -->
                <div class="dashboard-col">
                    <div class="section-card">
                        <div class="section-header">
                            <h2>Spending by Category</h2>
                            <a href="#" class="view-all">Details →</a>
                        </div>
                        <div class="donut-wrapper">
                            <canvas id="spendingChart" aria-label="Spending by category donut chart"></canvas>
                        </div>
                        <div class="donut-legend">
                            <div class="donut-legend-item">
                                <span class="donut-legend-dot" style="background: var(--green);"></span>
                                <span class="donut-legend-label">Food & Dining</span>
                                <span class="donut-legend-pct">35%</span>
                            </div>
                            <div class="donut-legend-item">
                                <span class="donut-legend-dot" style="background: var(--amber);"></span>
                                <span class="donut-legend-label">Transportation</span>
                                <span class="donut-legend-pct">25%</span>
                            </div>
                            <div class="donut-legend-item">
                                <span class="donut-legend-dot" style="background: var(--indigo);"></span>
                                <span class="donut-legend-label">Shopping</span>
                                <span class="donut-legend-pct">20%</span>
                            </div>
                            <div class="donut-legend-item">
                                <span class="donut-legend-dot" style="background: var(--red);"></span>
                                <span class="donut-legend-label">Entertainment</span>
                                <span class="donut-legend-pct">12%</span>
                            </div>
                            <div class="donut-legend-item">
                                <span class="donut-legend-dot" style="background: #8B5CF6;"></span>
                                <span class="donut-legend-label">Bills</span>
                                <span class="donut-legend-pct">8%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.dashboard-two-columns -->
        </main>
    </div>

    <!-- Modal New Budget -->
    <div class="modal-overlay" id="budgetModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Create New Budget</h2>
                <button class="modal-close" id="closeModalBtn">×</button>
            </div>
            <form class="modal-form" id="budgetForm">
                <div class="form-group">
                    <label>Budget Name</label>
                    <input type="text" placeholder="e.g., Groceries, Transport...">
                </div>
                <div class="form-group">
                    <label>Budget Type</label>
                    <div class="selector-group">
                        <button type="button" class="selector-btn active">Individual</button>
                        <button type="button" class="selector-btn">Shared</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Period</label>
                    <div class="date-range">
                        <input type="date" placeholder="From">
                        <span>→</span>
                        <input type="date" placeholder="To">
                    </div>
                </div>
                <div class="form-group">
                    <label>Budget Limit ($)</label>
                    <input type="number" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Alert Threshold</label>
                    <div class="alert-buttons">
                        <button type="button" class="alert-btn">50%</button>
                        <button type="button" class="alert-btn">75%</button>
                        <button type="button" class="alert-btn">90%</button>
                        <button type="button" class="alert-btn">100%</button>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Create Budget</button>
                </div>
            </form>
        </div>
    </div>

<script src="frontend/js/dashboard.js"></script>
<script>
    (function() {
        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        function buildChart() {
            var ctx = document.getElementById('spendingChart');
            if (!ctx) return;

            var colors = [
                cssVar('--green') || '#10B981',
                cssVar('--amber') || '#F59E0B',
                cssVar('--indigo') || '#6366F1',
                cssVar('--red') || '#EF4444',
                '#8B5CF6'
            ];

            if (window._spendingChart) {
                window._spendingChart.destroy();
            }

            window._spendingChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Food & Dining', 'Transportation', 'Shopping', 'Entertainment', 'Bills'],
                    datasets: [{
                        data: [35, 25, 20, 12, 8],
                        backgroundColor: colors,
                        borderWidth: 3,
                        borderColor: 'transparent',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '62%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return '  ' + ctx.label + ': ' + ctx.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        }

        buildChart();

        var themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                setTimeout(buildChart, 50);
            });
        }
    })();
</script>
</body>

</html>
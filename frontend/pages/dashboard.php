<?php
require_once __DIR__ . '/../includes/header.php';
 
?>
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


        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
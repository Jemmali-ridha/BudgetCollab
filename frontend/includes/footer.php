    <!-- Modal New Budget -->
    <div class="modal-overlay" id="budgetModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2>Create New Budget</h2>
                <button class="modal-close" id="closeModalBtn">×</button>
            </div>
            <form class="modal-form" method="POST" action="index.php?page=budgets&action=create">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="budget_type" id="budgetTypeInput" value="individual">
                <input type="hidden" name="alert_threshold" id="alertThresholdInput" value="75%">

                <div class="form-group">
                    <label>Budget Name</label>
                    <input type="text" name="budget_name" placeholder="e.g., Groceries, Transport...">
                </div>
                <div class="form-group">
                    <label>Budget Type</label>
                    <div class="selector-group">
                        <button type="button" class="selector-btn active" data-type="individual">Individual</button>
                        <button type="button" class="selector-btn" data-type="shared">Shared</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Period</label>
                    <div class="date-range">
                        <input type="date" name="start_date">
                        <span>→</span>
                        <input type="date" name="end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label>Budget Limit ($)</label>
                    <input type="number" name="total_limit" placeholder="0.00" step="0.001" min="0">
                </div>
                <div class="form-group">
                    <label>Alert Threshold</label>
                    <div class="alert-buttons">
                        <button type="button" class="alert-btn" data-threshold="50%">50%</button>
                        <button type="button" class="alert-btn active" data-threshold="75%">75%</button>
                        <button type="button" class="alert-btn" data-threshold="90%">90%</button>
                        <button type="button" class="alert-btn" data-threshold="100%">100%</button>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn-primary">Create Budget</button>
                </div>
            </form>
        </div>
    </div>
</main>

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

            document.querySelectorAll('.selector-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.selector-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('budgetTypeInput').value = btn.dataset.type;
            });
        });

        // Alert threshold
        document.querySelectorAll('.alert-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.alert-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('alertThresholdInput').value = btn.dataset.threshold;
            });
        });

        // Modal open/close
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            document.getElementById('budgetModal').classList.remove('active');
        });
        document.getElementById('cancelModalBtn').addEventListener('click', () => {
            document.getElementById('budgetModal').classList.remove('active');
        });
</script>
</body>

</html>
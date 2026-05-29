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
</script>
</body>

</html>
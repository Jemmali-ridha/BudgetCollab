<?php

class Dashboard
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ── Stat Cards ──────────────────────────────────────────────

    public function getStats(int $userId): array
    {
        // Current month
        $stmt = $this->pdo->prepare('
            SELECT
                COALESCE(SUM(CASE WHEN type_transaction = "revenu"  THEN montant ELSE 0 END), 0) AS total_revenu,
                COALESCE(SUM(CASE WHEN type_transaction = "depense" THEN montant ELSE 0 END), 0) AS total_depense
            FROM transactions
            WHERE id_utilisateur = ?
              AND MONTH(date_transaction) = MONTH(CURDATE())
              AND YEAR(date_transaction)  = YEAR(CURDATE())
        ');
        $stmt->execute([$userId]);
        $current = $stmt->fetch();

        // Last month
        $stmt2 = $this->pdo->prepare('
            SELECT
                COALESCE(SUM(CASE WHEN type_transaction = "revenu"  THEN montant ELSE 0 END), 0) AS total_revenu,
                COALESCE(SUM(CASE WHEN type_transaction = "depense" THEN montant ELSE 0 END), 0) AS total_depense
            FROM transactions
            WHERE id_utilisateur = ?
              AND MONTH(date_transaction) = MONTH(CURDATE() - INTERVAL 1 MONTH)
              AND YEAR(date_transaction)  = YEAR(CURDATE()  - INTERVAL 1 MONTH)
        ');
        $stmt2->execute([$userId]);
        $last = $stmt2->fetch();

        $revenu  = (float) $current['total_revenu'];
        $depense = (float) $current['total_depense'];
        $balance = $revenu - $depense;

        $lastRevenu  = (float) $last['total_revenu'];
        $lastDepense = (float) $last['total_depense'];
        $lastBalance = $lastRevenu - $lastDepense;

        // Budget usage: spent vs total_limit across active budgets
        $stmt3 = $this->pdo->prepare('
            SELECT
                COALESCE(SUM(b.total_limit), 0) AS total_limit,
                COALESCE(SUM(CASE WHEN t.type_transaction = "depense" THEN t.montant ELSE 0 END), 0)
              - COALESCE(SUM(CASE WHEN t.type_transaction = "revenu"  THEN t.montant ELSE 0 END), 0) AS total_spent
            FROM budgets b
            LEFT JOIN transactions t ON t.id_budget = b.id_budget
            WHERE b.created_by = ?
        ');
        $stmt3->execute([$userId]);
        $usage = $stmt3->fetch();

        $totalLimit = (float) $usage['total_limit'];
        $totalSpent = (float) $usage['total_spent'];
        $budgetPct  = $totalLimit > 0 ? round(($totalSpent / $totalLimit) * 100, 1) : 0;

        return [
            'revenu'       => $revenu,
            'depense'      => $depense,
            'balance'      => $balance,
            'budget_usage' => $budgetPct,
            'trend_revenu'  => $this->trend($revenu,  $lastRevenu),
            'trend_depense' => $this->trend($depense, $lastDepense),
            'trend_balance' => $this->trend($balance, $lastBalance),
        ];
    }

    private function trend(float $current, float $last): string
    {
        if ($last == 0) return '+0%';
        $pct = round((($current - $last) / abs($last)) * 100, 1);
        return ($pct >= 0 ? '+' : '') . $pct . '%';
    }

    // ── Budget Progress ──────────────────────────────────────────

    public function getBudgetProgress(int $userId, int $limit = 5): array
    {
        $stmt = $this->pdo->prepare('
            SELECT b.id_budget, b.budget_name, b.total_limit,
                COALESCE(SUM(CASE WHEN t.type_transaction = "depense" THEN t.montant ELSE 0 END), 0)
              - COALESCE(SUM(CASE WHEN t.type_transaction = "revenu"  THEN t.montant ELSE 0 END), 0) AS spent
            FROM budgets b
            LEFT JOIN transactions t ON t.id_budget = b.id_budget
            WHERE b.created_by = ?
            GROUP BY b.id_budget
            ORDER BY spent DESC
            LIMIT ?
        ');
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ── Spending by Category ─────────────────────────────────────

    public function getSpendingByCategory(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT c.nom_categorie, c.couleur,
                   COALESCE(SUM(t.montant), 0) AS total
            FROM transactions t
            JOIN categories c ON t.id_categorie = c.id_categorie
            WHERE t.id_utilisateur = ?
              AND t.type_transaction = "depense"
              AND MONTH(t.date_transaction) = MONTH(CURDATE())
              AND YEAR(t.date_transaction)  = YEAR(CURDATE())
            GROUP BY c.id_categorie
            ORDER BY total DESC
        ');
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grandTotal = array_sum(array_column($rows, 'total'));

        return array_map(function ($row) use ($grandTotal) {
            $row['pct'] = $grandTotal > 0
                ? round(($row['total'] / $grandTotal) * 100, 1)
                : 0;
            return $row;
        }, $rows);
    }
}
<?php
require_once __DIR__ . '/../config/database.php';

class Budget
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }

    public function getSpent(int $budgetId): float
    {
        $stmt = $this->pdo->prepare('
            SELECT COALESCE(SUM(montant), 0) as spent
            FROM transactions
            WHERE id_budget = ? AND type_transaction = "depense"
        ');
        $stmt->execute([$budgetId]);
        return (float) $stmt->fetch()['spent'];
    }

        public function getAllWithSpent(int $userId): array
        {
            $stmt = $this->pdo->prepare('
                SELECT b.*,
                    COALESCE(SUM(CASE WHEN t.type_transaction = "depense" THEN t.montant ELSE 0 END), 0) AS total_depense,
                    COALESCE(SUM(CASE WHEN t.type_transaction = "revenu"  THEN t.montant ELSE 0 END), 0) AS total_revenu,
                    COALESCE(SUM(CASE WHEN t.type_transaction = "depense" THEN t.montant ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN t.type_transaction = "revenu"  THEN t.montant ELSE 0 END), 0) AS spent
                FROM budgets b
                LEFT JOIN transactions t ON t.id_budget = b.id_budget
                WHERE b.created_by = ? AND b.budget_type = "individual"
                GROUP BY b.id_budget
                ORDER BY b.start_date DESC
            ');
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getSharedWithSpent(int $userId): array
        {
            $stmt = $this->pdo->prepare('
                SELECT DISTINCT
                    b.*,
                    COALESCE(SUM(CASE WHEN t.type_transaction = "depense" THEN t.montant ELSE 0 END), 0)
                    - COALESCE(SUM(CASE WHEN t.type_transaction = "revenu" THEN t.montant ELSE 0 END), 0) AS spent
                FROM budgets b
                INNER JOIN budget_members bm
                    ON bm.id_budget = b.id_budget
                LEFT JOIN transactions t
                    ON t.id_budget = b.id_budget
                WHERE bm.id_utilisateur = ?
                AND b.budget_type = "shared"
                GROUP BY b.id_budget
                ORDER BY b.start_date DESC
            ');

            $stmt->execute([$userId]);
            $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($budgets as &$budget) {
                $budget['members'] = $this->getMembersByBudget($budget['id_budget']);
            }

            return $budgets;
        }

        private function getMembersByBudget(int $budgetId): array
        {
            $stmt = $this->pdo->prepare('
                SELECT u.id_utilisateur, u.nom, u.prenom
                FROM transactions t
                JOIN utilisateurs u ON t.id_utilisateur = u.id_utilisateur
                WHERE t.id_budget = ?
                GROUP BY u.id_utilisateur
            ');
            $stmt->execute([$budgetId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getRecentActivityByUser(int $userId, int $limit = 10): array
        {
            $stmt = $this->pdo->prepare('
                SELECT
                    t.montant,
                    t.type_transaction,
                    t.description,
                    t.date_creation,
                    b.budget_name,
                    u.nom,
                    u.prenom
                FROM transactions t
                JOIN budgets b
                    ON t.id_budget = b.id_budget
                JOIN utilisateurs u
                    ON t.id_utilisateur = u.id_utilisateur
                JOIN budget_members bm
                    ON b.id_budget = bm.id_budget
                WHERE bm.id_utilisateur = ?
                AND b.budget_type = "shared"
                ORDER BY t.date_creation DESC
                LIMIT ?
            ');

            $stmt->execute([$userId, $limit]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO budgets 
                (budget_name, budget_type, start_date, end_date, total_limit, alert_threshold, created_by)
            VALUES 
                (:budget_name, :budget_type, :start_date, :end_date, :total_limit, :alert_threshold, :created_by)
        ');

        return $stmt->execute([
            ':budget_name'     => $data['budget_name'],
            ':budget_type'     => $data['budget_type'],
            ':start_date'      => $data['start_date'],
            ':end_date'        => $data['end_date'],
            ':total_limit'     => $data['total_limit'] ?: null,
            ':alert_threshold' => $data['alert_threshold'],
            ':created_by'      => $data['created_by'],
        ]);
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM budgets 
            WHERE created_by = ?
            ORDER BY start_date DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $budgetId): array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM budgets WHERE id_budget = ?');
        $stmt->execute([$budgetId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete(int $budgetId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM budgets WHERE id_budget = ? AND created_by = ?
        ');
        return $stmt->execute([$budgetId, $userId]);
    }
}
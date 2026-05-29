<?php
require_once __DIR__ . '/../config/database.php';

class Budget
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
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
            WHERE created_by = ? AND status = "active"
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
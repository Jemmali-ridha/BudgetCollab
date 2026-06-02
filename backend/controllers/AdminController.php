<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

class AdminController
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = getDB();
    }


    private function getStats(): array
    {
        $totalUsers = $this->pdo->query(
            'SELECT COUNT(*) FROM utilisateurs'
        )->fetchColumn();

        $totalRevenue = $this->pdo->query(
            "SELECT COALESCE(SUM(montant), 0) FROM transactions WHERE type_transaction = 'revenu'"
        )->fetchColumn();

        $activeBudgets = $this->pdo->query(
            "SELECT COUNT(*) FROM budgets WHERE end_date >= CURDATE()"
        )->fetchColumn();

        $transactionsToday = $this->pdo->query(
            "SELECT COUNT(*) FROM transactions WHERE DATE(date_creation) = CURDATE()"
        )->fetchColumn();

        return [
            'total_users'         => (int) $totalUsers,
            'total_revenue'       => (float) $totalRevenue,
            'active_budgets'      => (int) $activeBudgets,
            'transactions_today'  => (int) $transactionsToday,
        ];
    }

    private function getUsers(): array
    {
        $stmt = $this->pdo->query(
            'SELECT u.id_utilisateur, u.nom, u.prenom, u.email,u.status,
                    u.date_creation, u.date_modification,
                    r.nom_role
             FROM utilisateurs u
             LEFT JOIN roles r ON u.id_role = r.id_role
             ORDER BY u.date_creation DESC'
        );
        return $stmt->fetchAll();
    }


        public function approveUser(): void
        {
            $userId = (int)($_POST['user_id'] ?? 0);

            $stmt = getDB()->prepare("
                UPDATE utilisateurs
                SET status = 'active'
                WHERE id_utilisateur = ?
            ");

            $stmt->execute([$userId]);

            flashMessage('success', 'User approved.');

            header('Location: index.php?page=admin');
            exit;
        }

        public function suspendUser(): void
            {
                $userId = (int)($_POST['user_id'] ?? 0);

                $stmt = getDB()->prepare("
                    UPDATE utilisateurs
                    SET status = 'suspended'
                    WHERE id_utilisateur = ?
                ");

                $stmt->execute([$userId]);

                flashMessage('success', 'User suspended.');

                header('Location: index.php?page=admin');
                exit;
            }

        public function deleteUser(): void
{
    requiertConnexion();

    $userId = (int)($_POST['user_id'] ?? 0);

    if (!$userId) {
        flashMessage('danger', 'Invalid user.');
        header('Location: index.php?page=admin');
        exit;
    }

    $stmt = getDB()->prepare("
        DELETE FROM utilisateurs
        WHERE id_utilisateur = ?
    ");

    $stmt->execute([$userId]);

    flashMessage('success', 'User deleted.');

    header('Location: index.php?page=admin');
    exit;
}


    private function getRecentActivity(): array
    {
        $stmt = $this->pdo->query(
            "SELECT u.nom, u.prenom, t.type_transaction, t.montant,
                    t.date_creation, b.budget_name,
                    'transaction' AS event_type
             FROM transactions t
             JOIN utilisateurs u ON t.id_utilisateur = u.id_utilisateur
             JOIN budgets b      ON t.id_budget = b.id_budget
             UNION ALL
             SELECT u.nom, u.prenom, NULL, NULL,
                    u.date_creation, NULL,
                    'inscription'
             FROM utilisateurs u
             ORDER BY date_creation DESC
             LIMIT 10"
        );
        return $stmt->fetchAll();
    }


    private function getSharedBudgets(): array
    {
        $stmt = $this->pdo->query(
            "SELECT b.id_budget, b.budget_name, b.end_date,
                    b.total_limit, b.created_by,
                    COUNT(bm.id_utilisateur) AS nb_membres,
                    COALESCE(SUM(t.montant), 0) AS total_depense
             FROM budgets b
             LEFT JOIN budget_members bm ON bm.id_budget = b.id_budget
             LEFT JOIN transactions t    ON t.id_budget = b.id_budget
                                        AND t.type_transaction = 'depense'
             WHERE b.budget_type = 'shared'
             GROUP BY b.id_budget
             ORDER BY b.id_budget DESC
             LIMIT 5"
        );
        return $stmt->fetchAll();
    }


    private function handlePost(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!verifier_csrf($token)) {
            flashMessage('danger', 'Invalid request (CSRF).');
            header('Location: index.php?page=admin');
            exit;
        }

        $action = $_POST['action'] ?? '';
        $userId = (int) ($_POST['user_id'] ?? 0);
        $currentUserId = (int) ($_SESSION['user_id'] ?? 0);

        if ($userId === $currentUserId && in_array($action, ['delete', 'change_role'])) {
            flashMessage('danger', 'You cannot modify your own account.');
            header('Location: index.php?page=admin');
            exit;
        }

        switch ($action) {

            case 'approve_user':
                flashMessage('success', 'User approved successfully.');
                break;

            case 'change_role':
                $newRole = $_POST['role'] ?? '';
                $roleMap = ['admin' => 1, 'utilisateur' => 2];
                if (!isset($roleMap[$newRole])) {
                    flashMessage('danger', 'Unknown role.');
                    break;
                }
                $stmt = $this->pdo->prepare(
                    'UPDATE utilisateurs SET id_role = ? WHERE id_utilisateur = ?'
                );
                $stmt->execute([$roleMap[$newRole], $userId]);
                flashMessage('success', 'Role updated successfully.');
                break;

            case 'delete':
                $stmt = $this->pdo->prepare(
                    'DELETE FROM utilisateurs WHERE id_utilisateur = ?'
                );
                $stmt->execute([$userId]);
                flashMessage('success', 'User deleted successfully.');
                break;

            default:
                flashMessage('danger', 'Unknown action.');
        }

        header('Location: index.php?page=admin');
        exit;
    }


    public function show(): void
    {
        requiertConnexion();

        if (!estAdmin()) {
            flashMessage('danger', 'Access denied: admin only.');
            header('Location: index.php?page=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePost();
        }

        $stats          = $this->getStats();
        $users          = $this->getUsers();
        $recentActivity = $this->getRecentActivity();
        $sharedBudgets  = $this->getSharedBudgets();
        
        $flash = getFlash();
        
        $pageTitle      = 'Admin';

        require_once __DIR__ . '/../../frontend/pages/admin.php';
    }
}
<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Invitation.php';

class SharedBudgetsController
{
    private Budget $budgetModel;
    private Invitation $invitationModel;

    public function __construct()
    {
        $this->budgetModel = new Budget();
        $this->invitationModel = new Invitation(getDB());
    }

    public function show(): void
    {
        requiertConnexion();

        $userId = $_SESSION['user_id'];

        $sharedBudgets = $this->budgetModel->getSharedWithSpent($userId);
        
        $recentActivity = $this->budgetModel->getRecentActivityByUser($userId);
        
        $pendingInvites = $this->invitationModel->getPendingByUser($userId);
        
        $allUsers = $this->getAllUsersExceptCurrent($userId);

        $flash = getFlash();
        $pageTitle = "Shared Budgets";

        require_once __DIR__ . '/../../frontend/pages/shared_budgets.php';
    }
    
    private function getAllUsersExceptCurrent(int $currentUserId): array
    {
        $pdo = getDB();
        $stmt = $pdo->prepare('
            SELECT id_utilisateur, nom, prenom, email 
            FROM utilisateurs 
            WHERE id_utilisateur != ? 
            ORDER BY nom, prenom
        ');
        $stmt->execute([$currentUserId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function delete(): void
    {
        requiertConnexion();
        
        $budgetId = (int)($_GET['id'] ?? 0);
        $userId = $_SESSION['user_id'];
        
        if (!$budgetId) {
            flashMessage('danger', 'Invalid budget.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        $pdo = getDB();
        
        $checkStmt = $pdo->prepare('
            SELECT created_by FROM budgets 
            WHERE id_budget = ? AND budget_type = "shared"
        ');
        $checkStmt->execute([$budgetId]);
        $budget = $checkStmt->fetch();
        
        if (!$budget) {
            flashMessage('danger', 'Budget not found.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        if ($budget['created_by'] != $userId) {
            flashMessage('danger', 'You are not authorized to delete this budget.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        $deleteStmt = $pdo->prepare('DELETE FROM budgets WHERE id_budget = ?');
        $success = $deleteStmt->execute([$budgetId]);
        
        if ($success) {
            flashMessage('success', 'Shared budget deleted successfully.');
        } else {
            flashMessage('danger', 'Error deleting budget.');
        }
        
        header('Location: index.php?page=shared-budgets');
        exit;
    }
}
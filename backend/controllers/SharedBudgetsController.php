<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Invitation.php';


class SharedBudgetsController
{
    private Budget $model;
    private Invitation $Invitation;


    public function __construct()
    {
        $this->model = new Budget(getDB());
        $this->Invitation = new Invitation(getDB());
    }

    public function show(): void
    {
        requiertConnexion();

        $userId = $_SESSION['user_id'];

        $sharedBudgets  = $this->model->getSharedWithSpent($userId);
        $recentActivity = $this->model->getRecentActivityByUser($userId);
        $pendingInvites = $this->Invitation->getPendingByUser($userId);
        
        // 🔴 AJOUTER CETTE LIGNE - Récupérer tous les utilisateurs
        $allUsers = $this->getAllUsersExceptCurrent($userId);

        $flash          = getFlash();
        $pageTitle      = "Shared Budgets";

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
    
    public function invite(): void
    {
        requiertConnexion();
        
        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Token invalide.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        $budgetId = (int)($_POST['budget_id'] ?? 0);
        $userIds = $_POST['users'] ?? [];
        
        if (!$budgetId) {
            flashMessage('danger', 'Budget invalide.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        if (empty($userIds)) {
            flashMessage('danger', 'Veuillez sélectionner au moins un utilisateur à inviter.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        $invitedCount = 0;
        foreach ($userIds as $userId) {
            if ($this->createInvitation($budgetId, (int)$userId)) {
                $invitedCount++;
            }
        }
        
        flashMessage('success', $invitedCount . ' invitation(s) envoyée(s) avec succès.');
        
        header('Location: index.php?page=shared-budgets');
        exit;
    }
    
    // createInvitation()
private function createInvitation(int $budgetId, int $userId): bool
{
    $pdo = getDB();
    $invitedBy = $_SESSION['user_id'];

    $stmt = $pdo->prepare('
        INSERT IGNORE INTO budget_invitations (id_budget, invited_by, invited_user)
        VALUES (?, ?, ?)
    ');
    return $stmt->execute([$budgetId, $invitedBy, $userId]);
}

// accept() — no token, use id instead
public function accept(): void
{
    requiertConnexion();

    $inviteId = (int) ($_GET['id'] ?? 0);

    if (!$inviteId) {
        flashMessage('danger', 'Invalid invitation.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }

    $pdo = getDB();

    $stmt = $pdo->prepare('
        SELECT * FROM budget_invitations
        WHERE id_invitation = ? AND invited_user = ? AND status = "pending"
    ');
    $stmt->execute([$inviteId, $_SESSION['user_id']]);
    $invite = $stmt->fetch();

    if (!$invite) {
        flashMessage('danger', 'Invalid or already processed invitation.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }

    // Add to budget_members
    $pdo->prepare('
        INSERT INTO budget_members (id_budget, id_utilisateur)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE joined_at = joined_at
    ')->execute([$invite['id_budget'], $_SESSION['user_id']]);

    // Mark accepted
    $pdo->prepare('
        UPDATE budget_invitations SET status = "accepted"
        WHERE id_invitation = ?
    ')->execute([$inviteId]);

    flashMessage('success', 'You have joined the budget.');
    header('Location: index.php?page=shared-budgets');
    exit;
}

// decline()
public function decline(): void
{
    requiertConnexion();

    $inviteId = (int) ($_GET['id'] ?? 0);

    $pdo = getDB();
    $stmt = $pdo->prepare('
        UPDATE budget_invitations SET status = "declined"
        WHERE id_invitation = ? AND invited_user = ?
    ');
    $success = $stmt->execute([$inviteId, $_SESSION['user_id']]);

    if (request_is_ajax()) {
        echo json_encode(['success' => $success]);
        exit;
    }

    flashMessage('success', 'Invitation declined.');
    header('Location: index.php?page=shared-budgets');
    exit;
}
    

    public function delete(): void
{
    requiertConnexion();
    
    $budgetId = (int)($_GET['id'] ?? 0);
    $userId = $_SESSION['user_id'];
    
    if (!$budgetId) {
        flashMessage('danger', 'Budget invalide.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }
    
    // Vérifier que l'utilisateur est bien le créateur du budget
    $pdo = getDB();
    $checkStmt = $pdo->prepare('
        SELECT created_by FROM budgets 
        WHERE id_budget = ? AND budget_type = "shared"
    ');
    $checkStmt->execute([$budgetId]);
    $budget = $checkStmt->fetch();
    
    if (!$budget) {
        flashMessage('danger', 'Budget non trouvé.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }
    
    if ($budget['created_by'] != $userId) {
        flashMessage('danger', 'Vous n\'êtes pas autorisé à supprimer ce budget.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }
    
    // Supprimer le budget (les transactions et membres seront supprimés en cascade)
    $deleteStmt = $pdo->prepare('DELETE FROM budgets WHERE id_budget = ?');
    $success = $deleteStmt->execute([$budgetId]);
    
    if ($success) {
        flashMessage('success', 'Budget partagé supprimé avec succès.');
    } else {
        flashMessage('danger', 'Erreur lors de la suppression du budget.');
    }
    
    header('Location: index.php?page=shared-budgets');
    exit;
}
}
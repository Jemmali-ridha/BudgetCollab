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
    
    private function createInvitation(int $budgetId, int $userId): bool
    {
        $pdo = getDB();
        $token = bin2hex(random_bytes(32));
        $invitedBy = $_SESSION['user_id'];
        
        $stmt = $pdo->prepare('
            INSERT INTO invitations (id_budget, id_invite, id_invitant, token, statut, date_expiration)
            VALUES (?, ?, ?, ?, "pending", DATE_ADD(NOW(), INTERVAL 7 DAY))
        ');
        
        return $stmt->execute([$budgetId, $userId, $invitedBy, $token]);
    }
    
    public function decline(): void
    {
        requiertConnexion();
        
        $inviteId = (int)($_GET['id'] ?? 0);
        
        if (!$inviteId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid invitation ID']);
            return;
        }
        
        $pdo = getDB();
        $stmt = $pdo->prepare('
            UPDATE invitations 
            SET statut = "declined" 
            WHERE id_invitation = ? AND id_invite = ?
        ');
        
        $success = $stmt->execute([$inviteId, $_SESSION['user_id']]);
        
        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Could not decline invitation']);
        }
        exit;
    }
    
    private function getPendingInvites(int $userId): array
    {
        $pdo = getDB();
        $stmt = $pdo->prepare('
            SELECT i.*, 
                   b.budget_name, 
                   b.total_limit,
                   u.nom as inviter_nom, 
                   u.prenom as inviter_prenom,
                   (SELECT COUNT(*) FROM budget_members bm WHERE bm.id_budget = i.id_budget) as member_count
            FROM invitations i
            JOIN budgets b ON i.id_budget = b.id_budget
            JOIN utilisateurs u ON i.id_invitant = u.id_utilisateur
            WHERE i.id_invite = ? AND i.statut = "pending" AND i.date_expiration > NOW()
            ORDER BY i.date_creation DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function accept(): void
    {
        requiertConnexion();
        
        $token = $_GET['token'] ?? '';
        
        if (!$token) {
            flashMessage('danger', 'Token d\'invitation invalide.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        $pdo = getDB();
        
        $stmt = $pdo->prepare('
            SELECT i.*, b.budget_type 
            FROM invitations i
            JOIN budgets b ON i.id_budget = b.id_budget
            WHERE i.token = ? AND i.id_invite = ? AND i.statut = "pending" AND i.date_expiration > NOW()
        ');
        $stmt->execute([$token, $_SESSION['user_id']]);
        $invite = $stmt->fetch();
        
        if (!$invite) {
            flashMessage('danger', 'Invitation invalide ou expirée.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }
        
        if ($invite['budget_type'] !== 'shared') {
            $updateStmt = $pdo->prepare('UPDATE budgets SET budget_type = "shared" WHERE id_budget = ?');
            $updateStmt->execute([$invite['id_budget']]);
        }
        
        $memberStmt = $pdo->prepare('
            INSERT INTO budget_members (id_budget, id_utilisateur, joined_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE joined_at = joined_at
        ');
        $memberStmt->execute([$invite['id_budget'], $_SESSION['user_id']]);
        
        $updateInvite = $pdo->prepare('UPDATE invitations SET statut = "accepted" WHERE id_invitation = ?');
        $updateInvite->execute([$invite['id_invitation']]);
        
        flashMessage('success', 'Vous avez rejoint le budget partagé avec succès !');
        header('Location: index.php?page=shared-budgets');
        exit;
    }
    
    private function getRecentActivity(int $userId): array
    {
        $pdo = getDB();
        $stmt = $pdo->prepare('
            SELECT t.*, 
                   u.nom, u.prenom,
                   b.budget_name,
                   CASE 
                       WHEN t.type_transaction = "depense" THEN "a ajouté une dépense"
                       ELSE "a ajouté un revenu"
                   END as action_label
            FROM transactions t
            JOIN budgets b ON t.id_budget = b.id_budget
            JOIN utilisateurs u ON t.id_utilisateur = u.id_utilisateur
            WHERE b.budget_type = "shared" 
               OR b.id_budget IN (SELECT id_budget FROM budget_members WHERE id_utilisateur = ?)
            ORDER BY t.date_creation DESC
            LIMIT 10
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
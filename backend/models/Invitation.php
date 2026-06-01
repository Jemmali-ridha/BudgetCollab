<?php

class Invitation
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function invite(int $budgetId, int $invitedBy, int $invitedUser): bool
    {
        $stmt = $this->pdo->prepare('
            INSERT IGNORE INTO budget_invitations (id_budget, invited_by, invited_user)
            VALUES (?, ?, ?)
        ');
        return $stmt->execute([$budgetId, $invitedBy, $invitedUser]);
    }

    public function getPendingByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT i.id_invitation, i.id_budget,
                   b.budget_name, b.total_limit,
                   u.nom AS inviter_nom, u.prenom AS inviter_prenom,
                   (SELECT COUNT(*) FROM budget_members bm WHERE bm.id_budget = i.id_budget) AS member_count
            FROM budget_invitations i
            JOIN budgets b      ON i.id_budget  = b.id_budget
            JOIN utilisateurs u ON i.invited_by = u.id_utilisateur
            WHERE i.invited_user = ? AND i.status = "pending"
            ORDER BY i.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function accept(int $invitationId, int $userId): bool
    {
        // Update status
        $stmt = $this->pdo->prepare('
            UPDATE budget_invitations
            SET status = "accepted"
            WHERE id_invitation = ? AND invited_user = ? AND status = "pending"
        ');
        $stmt->execute([$invitationId, $userId]);

        if ($stmt->rowCount() === 0) return false;

        // Add to budget_members
        $stmt2 = $this->pdo->prepare('
            SELECT id_budget FROM budget_invitations WHERE id_invitation = ?
        ');
        $stmt2->execute([$invitationId]);
        $budgetId = $stmt2->fetch()['id_budget'];

        $stmt3 = $this->pdo->prepare('
            INSERT IGNORE INTO budget_members (id_budget, id_utilisateur)
            VALUES (?, ?)
        ');
        return $stmt3->execute([$budgetId, $userId]);
    }

    public function decline(int $invitationId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE budget_invitations
            SET status = "declined"
            WHERE id_invitation = ? AND invited_user = ? AND status = "pending"
        ');
        return $stmt->execute([$invitationId, $userId]);
    }

    public function findUserByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare('
            SELECT id_utilisateur, nom, prenom, email
            FROM utilisateurs WHERE email = ?
        ');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function alreadyInvited(int $budgetId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM budget_invitations
            WHERE id_budget = ? AND invited_user = ? AND status = "pending"
        ');
        $stmt->execute([$budgetId, $userId]);
        return (bool) $stmt->fetchColumn();
    }

    public function isMember(int $budgetId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM budget_members
            WHERE id_budget = ? AND id_utilisateur = ?
        ');
        $stmt->execute([$budgetId, $userId]);
        return (bool) $stmt->fetchColumn();
    }
}
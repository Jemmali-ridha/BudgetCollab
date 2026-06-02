<?php

require_once __DIR__ . '/../includes/auth.php';

class ProfileController
{
    public function show(): void
    {
        requiertConnexion();

        $userId = $_SESSION['user_id'];
        $pdo    = getDB();

        $stmt = $pdo->prepare('SELECT * FROM utilisateurs WHERE id_utilisateur = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        // Stats
        $stats = [];

        $pdo->prepare('SELECT COUNT(*) FROM budgets WHERE created_by = ?')
            ->execute([$userId]);
        $stats['budgets'] = $pdo->query("SELECT COUNT(*) FROM budgets WHERE created_by = $userId")->fetchColumn();

        $stats['transactions'] = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE id_utilisateur = ?')
            ->execute([$userId]) ? $pdo->query("SELECT COUNT(*) FROM transactions WHERE id_utilisateur = $userId")->fetchColumn() : 0;

        $stats['shared'] = $pdo->query("SELECT COUNT(*) FROM budget_members WHERE id_utilisateur = $userId")->fetchColumn();

        $flash     = getFlash();

        $pageTitle = "Profile";

        require_once __DIR__ . '/../../frontend/pages/profile.php';
    }

    public function update(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token.');
            header('Location: index.php?page=profile');
            exit;
        }

        $userId    = $_SESSION['user_id'];
        $prenom    = trim($_POST['prenom']    ?? '');
        $nom       = trim($_POST['nom']       ?? '');
        $email     = trim($_POST['email']     ?? '');

        if (!$prenom || !$nom || !$email) {
            flashMessage('danger', 'First name, last name and email are required.');
            header('Location: index.php?page=profile');
            exit;
        }

        $stmt = getDB()->prepare('
            UPDATE utilisateurs SET prenom = ?, nom = ?, email = ?
            WHERE id_utilisateur = ?
        ');
        $stmt->execute([$prenom, $nom, $email, $userId]);

        $_SESSION['prenom'] = $prenom;
        $_SESSION['nom']    = $nom;
        $_SESSION['email']  = $email;

        flashMessage('success', 'Profile updated successfully.');
        header('Location: index.php?page=profile');
        exit;
    }

    public function password(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token.');
            header('Location: index.php?page=profile');
            exit;
        }

        $userId          = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $stmt = getDB()->prepare('SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur = ?');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if (!password_verify($currentPassword, $user['mot_de_passe'])) {
            flashMessage('danger', 'Current password is incorrect.');
            header('Location: index.php?page=profile');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            flashMessage('danger', 'New passwords do not match.');
            header('Location: index.php?page=profile');
            exit;
        }

        if (strlen($newPassword) < 8) {
            flashMessage('danger', 'Password must be at least 8 characters.');
            header('Location: index.php?page=profile');
            exit;
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        getDB()->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id_utilisateur = ?')
               ->execute([$hash, $userId]);

        flashMessage('success', 'Password updated successfully.');
        header('Location: index.php?page=profile');
        exit;
    }

    public function delete(): void
{
    requiertConnexion();

    if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
        flashMessage('danger', 'Invalid CSRF token.');
        header('Location: index.php?page=profile');
        exit;
    }

    $userId   = $_SESSION['user_id'];
    $password = $_POST['confirm_password'] ?? '';

    $pdo = getDB();

    // Verify password before deleting
    $stmt = $pdo->prepare('SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur = ?');
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!password_verify($password, $user['mot_de_passe'])) {
        flashMessage('danger', 'Incorrect password. Account not deleted.');
        header('Location: index.php?page=profile');
        exit;
    }

    // Delete in correct order to respect foreign keys
    $pdo->prepare('DELETE FROM budget_members     WHERE id_utilisateur = ?')->execute([$userId]);
    $pdo->prepare('DELETE FROM budget_invitations WHERE invited_user   = ? OR invited_by = ?')->execute([$userId, $userId]);
    $pdo->prepare('DELETE FROM transactions        WHERE id_utilisateur = ?')->execute([$userId]);
    $pdo->prepare('DELETE FROM budgets             WHERE created_by     = ?')->execute([$userId]);
    $pdo->prepare('DELETE FROM utilisateurs        WHERE id_utilisateur = ?')->execute([$userId]);

    session_destroy();

    header('Location: index.php?page=login');
    exit;
}
}
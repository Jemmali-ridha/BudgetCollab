<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Invitation.php';
require_once __DIR__ . '/../models/Budget.php';

class InvitationController
{
    private Invitation $model;
    private Budget $budgetModel;

    public function __construct()
    {
        $this->model = new Invitation(getDB());
        $this->budgetModel = new Budget(getDB());
    }

    // Creator sends invite by email
    public function send(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $budgetId = (int) ($_POST['id_budget'] ?? 0);
        $email    = trim($_POST['email'] ?? '');

        if (!$budgetId || !$email) {
            flashMessage('danger', 'Missing required fields.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        // Verify the current user owns this budget
        $budget = $this->budgetModel->getById($budgetId);
        if (!$budget || $budget['created_by'] !== $_SESSION['user_id']) {
            flashMessage('danger', 'You are not authorized to invite members to this budget.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        // Find invited user
        $invitedUser = $this->model->findUserByEmail($email);
        if (!$invitedUser) {
            flashMessage('danger', 'No user found with that email.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        // Can't invite yourself
        if ($invitedUser['id_utilisateur'] === $_SESSION['user_id']) {
            flashMessage('danger', 'You cannot invite yourself.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        // Already a member?
        if ($this->model->isMember($budgetId, $invitedUser['id_utilisateur'])) {
            flashMessage('danger', 'This user is already a member of this budget.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        // Already invited?
        if ($this->model->alreadyInvited($budgetId, $invitedUser['id_utilisateur'])) {
            flashMessage('danger', 'This user already has a pending invitation.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $ok = $this->model->invite($budgetId, $_SESSION['user_id'], $invitedUser['id_utilisateur']);

        if ($ok) {
            flashMessage('success', 'Invitation sent to ' . $invitedUser['prenom'] . ' ' . $invitedUser['nom'] . '.');
        } else {
            flashMessage('danger', 'Failed to send invitation.');
        }

        header('Location: index.php?page=shared-budgets');
        exit;
    }

    public function accept(): void
    {
        requiertConnexion();

        $invitationId = (int) ($_GET['id'] ?? 0);

        if (!$invitationId) {
            flashMessage('danger', 'Invalid invitation.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $ok = $this->model->accept($invitationId, $_SESSION['user_id']);

        if ($ok) {
            flashMessage('success', 'You have joined the budget.');
        } else {
            flashMessage('danger', 'Unable to accept this invitation.');
        }

        header('Location: index.php?page=shared-budgets');
        exit;
    }

    public function decline(): void
    {
        requiertConnexion();

        $invitationId = (int) ($_GET['id'] ?? 0);

        if (!$invitationId) {
            flashMessage('danger', 'Invalid invitation.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $ok = $this->model->decline($invitationId, $_SESSION['user_id']);

        if ($ok) {
            flashMessage('success', 'Invitation declined.');
        } else {
            flashMessage('danger', 'Unable to decline this invitation.');
        }

        header('Location: index.php?page=shared-budgets');
        exit;
    }
}
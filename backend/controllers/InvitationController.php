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
        $this->model = new Invitation();
        $this->budgetModel = new Budget(getDB());
    }

    public function send(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $budgetId = (int) ($_POST['budget_id'] ?? 0);
        $userIds = $_POST['users'] ?? [];

        if (!$budgetId || empty($userIds)) {
            flashMessage('danger', 'Missing required fields.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $budget = $this->budgetModel->getById($budgetId);
        if (!$budget || $budget['created_by'] !== $_SESSION['user_id']) {
            flashMessage('danger', 'You are not authorized to invite members to this budget.');
            header('Location: index.php?page=shared-budgets');
            exit;
        }

        $invitedCount = 0;
        foreach ($userIds as $invitedUserId) {
            $invitedUserId = (int)$invitedUserId;
            
            if ($invitedUserId === $_SESSION['user_id']) {
                continue;
            }

            if ($this->model->isMember($budgetId, $invitedUserId)) {
                continue;
            }

            if ($this->model->alreadyInvited($budgetId, $invitedUserId)) {
                continue;
            }

            if ($this->model->invite($budgetId, $_SESSION['user_id'], $invitedUserId)) {
                $invitedCount++;
            }
        }

        if ($invitedCount > 0) {
            flashMessage('success', $invitedCount . ' invitation(s) sent successfully.');
        } else {
            flashMessage('danger', 'No valid invitations were sent.');
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
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? false) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid invitation ID']);
            } else {
                flashMessage('danger', 'Invalid invitation.');
                header('Location: index.php?page=shared-budgets');
            }
            exit;
        }

        $ok = $this->model->decline($invitationId, $_SESSION['user_id']);

        if ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? false) {
            echo json_encode(['success' => $ok]);
            exit;
        }

        flashMessage($ok ? 'success' : 'danger', 
                    $ok ? 'Invitation declined.' : 'Unable to decline this invitation.');
        header('Location: index.php?page=shared-budgets');
        exit;
    }
}
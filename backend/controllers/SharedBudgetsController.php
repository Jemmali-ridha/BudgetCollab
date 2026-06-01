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

        $flash          = getFlash();
        $pageTitle      = "Shared Budgets";

        require_once __DIR__ . '/../../frontend/pages/shared_budgets.php';
    }
}
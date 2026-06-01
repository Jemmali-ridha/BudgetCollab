<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Budget.php';

class SharedBudgetsController
{
    private Budget $model;

    public function __construct()
    {
        $this->model = new Budget(getDB());
    }

    public function show(): void
    {
        requiertConnexion();

        $userId        = $_SESSION['user_id'];
        $sharedBudgets = $this->model->getSharedWithSpent($userId);
        $pageTitle     = "Shared Budgets";

        require_once __DIR__ . '/../../frontend/pages/shared_budgets.php';
    }
}
<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Dashboard.php';

class DashboardController
{
    private Dashboard $model;

    public function __construct()
    {
        $this->model = new Dashboard(getDB());
    }

    public function show(): void
    {
        requiertConnexion();

        $userId = $_SESSION['user_id'];

        $stats          = $this->model->getStats($userId);
        $budgetProgress = $this->model->getBudgetProgress($userId);
        $spendingByCategory = $this->model->getSpendingByCategory($userId);
        $flash          = getFlash();
        $pageTitle      = "Dashboard";

        require_once __DIR__ . '/../../frontend/pages/dashboard.php';
    }
}
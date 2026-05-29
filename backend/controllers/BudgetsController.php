<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';

class BudgetsController
{
    public function show(): void
    {
        // 🔒 Protect route
        requiertConnexion();

        $pageTitle = "Budgets";

        require_once __DIR__ . '/../../frontend/pages/budgets.php';
    }
}

<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';

class SharedBudgetsController
{
    public function show(): void
    {
        // 🔒 Protect route
        requiertConnexion();

        $pageTitle = "Shared Budgets";

        require_once __DIR__ . '/../../frontend/pages/shared_budgets.php';
    }
}

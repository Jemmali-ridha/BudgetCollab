<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';

class DashboardController
{
    public function show(): void
    {
        // 🔒 Protect route
        requiertConnexion();

        $pageTitle = "Dashboard";

        require_once __DIR__ . '/../../frontend/pages/dashboard.php';
    }
}

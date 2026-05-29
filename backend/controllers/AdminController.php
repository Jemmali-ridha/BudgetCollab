<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';

class AdminController
{
    public function show(): void
    {
        // 🔒 Protect route
        requiertConnexion();

        $pageTitle = "Admin";

        require_once __DIR__ . '/../../frontend/pages/admin.php';
    }
}

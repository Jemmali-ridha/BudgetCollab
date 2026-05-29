<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';

class TransactionsController
{
    public function show(): void
    {
        // 🔒 Protect route
        requiertConnexion();

        $pageTitle = "Transactions";

        require_once __DIR__ . '/../../frontend/pages/transactions.php';
    }
}

<?php
// controllers/DashboardController.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/User.php';


class ViewController
{

    public function Home(): void
    {
        require_once __DIR__ . '/../../frontend/pages/index.php';
    }

}
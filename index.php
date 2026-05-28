<?php

require_once __DIR__ . '/backend/includes/auth.php';
require_once __DIR__ . '/backend/config/database.php';

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? '';


// — Routage ——————————————————————————————

switch ($page) {

    case 'login':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $ctrl = new AuthController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
            $ctrl->handleLogin();
        } else {
            $ctrl->showLogin();
        }
        break;

    case 'register':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        $ctrl = new AuthController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'submit') {
            $ctrl->handleRegister();
        } else {
            $ctrl->showRegister();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

}

?>
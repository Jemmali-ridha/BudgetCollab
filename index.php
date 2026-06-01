<?php

require_once __DIR__ . '/backend/includes/auth.php';
require_once __DIR__ . '/backend/config/database.php';

$page = $_GET['page'] ?? 'view';
$action = $_GET['action'] ?? '';


// — Routage ——————————————————————————————

switch ($page) {


    case 'view':
        require_once __DIR__ . '/backend//controllers/ViewController.php';
        $ctrl = new ViewController();
        match($action) {
            default => $ctrl->Home()
        };
        break;


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

        case 'dashboard':
            require_once __DIR__ . '/backend/controllers/DashboardController.php';

            $ctrl = new DashboardController();

            match($action){
                default => $ctrl->show()
            };

            break;


        case 'transactions':
            require_once __DIR__ . '/backend/controllers/TransactionsController.php';
            $ctrl = new TransactionsController();

            match($action) {
                'create' => $ctrl->create(),
                'update' => $ctrl->update(),
                'delete' => $ctrl->delete(),
                default  => $ctrl->show(),
            };
            break;


        case 'budgets':
            require_once __DIR__ . '/backend/controllers/BudgetsController.php';
            $ctrl = new BudgetsController();

            match($action) {
                'create' => $ctrl->create(),
                'delete' => $ctrl->delete(),
                default  => $ctrl->show(),
            };
            break;


        case 'shared-budgets':
            require_once __DIR__ . '/backend/controllers/SharedBudgetsController.php';

            $ctrl = new SharedBudgetsController();

            match($action){
                default => $ctrl->show()
            };

            break;

        case 'invitations':
            require_once __DIR__ . '/backend/controllers/InvitationController.php';
            $ctrl = new InvitationController();

            match($action) {
                'send'    => $ctrl->send(),
                'accept'  => $ctrl->accept(),
                'decline' => $ctrl->decline(),
                default   => header('Location: index.php?page=shared-budgets'),
            };
            break;


        case 'categories':
            require_once __DIR__ . '/backend/controllers/CategoryController.php';

            $ctrl = new CategoryController();

            match($action) {
                'create' => $ctrl->create(),  // Handles both form display AND submission
                'update' => $ctrl->update(),
                'delete' => $ctrl->delete(),
                default  => $ctrl->show()
            };

            break;


        case 'admin':
            require_once __DIR__ . '/backend/controllers/AdminController.php';

            $ctrl = new AdminController();

            match($action){
                default => $ctrl->show()
            };

            break;
    


    case 'logout':
        require_once __DIR__ . '/backend/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

}

?>
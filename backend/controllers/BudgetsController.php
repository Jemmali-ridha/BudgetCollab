<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Budget.php';

class BudgetsController
{
    private Budget $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new Budget($pdo);
    }

    public function show(): void
    {
        requiertConnexion();

        $userId    = $_SESSION['user_id'];
        $budgets   = $this->model->getByUser($userId);
        $pageTitle = "Budgets";

        require_once __DIR__ . '/../../frontend/pages/budgets.php';
    }

    public function create(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token, please try again.');
            header('Location: index.php?page=budgets');
            exit;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            flashMessage('danger', implode('<br>', $errors));
            header('Location: index.php?page=budgets');
            exit;
        }

        $ok = $this->model->create([
            'budget_name'     => trim($_POST['budget_name']),
            'budget_type'     => $_POST['budget_type'],
            'start_date'      => $_POST['start_date'],
            'end_date'        => $_POST['end_date'],
            'total_limit'     => $_POST['total_limit'] ?? null,
            'alert_threshold' => $_POST['alert_threshold'],
            'created_by'      => $_SESSION['user_id'],
        ]);

        if ($ok) {
            flashMessage('success', 'Budget created successfully.');
        } else {
            flashMessage('danger', 'An error occurred while creating the budget.');
        }

        header('Location: index.php?page=budgets');
        exit;
    }

    public function delete(): void
    {
        requiertConnexion();

        $budgetId = (int) ($_GET['id'] ?? 0);

        if (!$budgetId) {
            flashMessage('danger', 'Budget not found.');
            header('Location: index.php?page=budgets');
            exit;
        }

        $ok = $this->model->delete($budgetId, $_SESSION['user_id']);

        if ($ok) {
            flashMessage('success', 'Budget deleted successfully.');
        } else {
            flashMessage('danger', 'Unable to delete this budget.');
        }

        header('Location: index.php?page=budgets');
        exit;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['budget_name'])) {
            $errors[] = 'Budget name is required.';
        }

        if (!in_array($data['budget_type'] ?? '', ['individual', 'shared'])) {
            $errors[] = 'Invalid budget type.';
        }

        if (empty($data['start_date']) || empty($data['end_date'])) {
            $errors[] = 'Start and end dates are required.';
        } elseif ($data['start_date'] > $data['end_date']) {
            $errors[] = 'Start date must be before end date.';
        }

        if (!in_array($data['alert_threshold'] ?? '', ['50%', '75%', '90%', '100%'])) {
            $errors[] = 'Invalid alert threshold.';
        }

        return $errors;
    }
}
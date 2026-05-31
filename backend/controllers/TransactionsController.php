<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Transaction.php';

class TransactionsController
{
    private Transaction $model;

    public function __construct()
    {
        global $pdo;
        $this->model = new Transaction($pdo);
    }

    public function show(): void
    {
        requiertConnexion();

        $pageTitle    = "Transactions";

        require_once __DIR__ . '/../../frontend/pages/transactions.php';
    }

    public function create(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token, please try again.');
            header('Location: index.php?page=transactions');
            exit;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            flashMessage('danger', implode('<br>', $errors));
            header('Location: index.php?page=transactions');
            exit;
        }

        $ok = $this->model->create([
            'id_utilisateur'   => $_SESSION['user_id'],
            'id_budget'        => (int) $_POST['id_budget'],
            'id_categorie'     => (int) $_POST['id_categorie'],
            'type_transaction' => $_POST['type_transaction'],
            'montant'          => (float) $_POST['montant'],
            'description'      => trim($_POST['description'] ?? ''),
            'date_transaction' => $_POST['date_transaction'],
        ]);

        if ($ok) {
            flashMessage('success', 'Transaction created successfully.');
        } else {
            flashMessage('danger', 'An error occurred while creating the transaction.');
        }

        header('Location: index.php?page=transactions');
        exit;
    }

    public function update(): void
    {
        requiertConnexion();

        if (!verifier_csrf($_POST['csrf_token'] ?? '')) {
            flashMessage('danger', 'Invalid CSRF token, please try again.');
            header('Location: index.php?page=transactions');
            exit;
        }

        $transactionId = (int) ($_POST['id_transaction'] ?? 0);

        if (!$transactionId) {
            flashMessage('danger', 'Transaction not found.');
            header('Location: index.php?page=transactions');
            exit;
        }

        $errors = $this->validate($_POST);

        if (!empty($errors)) {
            flashMessage('danger', implode('<br>', $errors));
            header('Location: index.php?page=transactions');
            exit;
        }

        $ok = $this->model->update($transactionId, $_SESSION['user_id'], [
            'id_budget'        => (int) $_POST['id_budget'],
            'id_categorie'     => (int) $_POST['id_categorie'],
            'type_transaction' => $_POST['type_transaction'],
            'montant'          => (float) $_POST['montant'],
            'description'      => trim($_POST['description'] ?? ''),
            'date_transaction' => $_POST['date_transaction'],
        ]);

        if ($ok) {
            flashMessage('success', 'Transaction updated successfully.');
        } else {
            flashMessage('danger', 'An error occurred while updating the transaction.');
        }

        header('Location: index.php?page=transactions');
        exit;
    }

    public function delete(): void
    {
        requiertConnexion();

        $transactionId = (int) ($_GET['id'] ?? 0);

        if (!$transactionId) {
            flashMessage('danger', 'Transaction not found.');
            header('Location: index.php?page=transactions');
            exit;
        }

        $ok = $this->model->delete($transactionId, $_SESSION['user_id']);

        if ($ok) {
            flashMessage('success', 'Transaction deleted successfully.');
        } else {
            flashMessage('danger', 'Unable to delete this transaction.');
        }

        header('Location: index.php?page=transactions');
        exit;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty($data['id_budget'])) {
            $errors[] = 'Budget is required.';
        }

        if (empty($data['id_categorie'])) {
            $errors[] = 'Category is required.';
        }

        if (!in_array($data['type_transaction'] ?? '', ['revenu', 'depense'])) {
            $errors[] = 'Invalid transaction type.';
        }

        if (empty($data['montant']) || (float) $data['montant'] <= 0) {
            $errors[] = 'Amount must be greater than 0.';
        }

        if (empty($data['date_transaction'])) {
            $errors[] = 'Transaction date is required.';
        }

        return $errors;
    }
}
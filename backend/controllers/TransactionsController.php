<?php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/Category.php';

class TransactionsController
{
    private Transaction $model;

    public function __construct()
    {
        $this->model = new Transaction();
    }

    public function show(): void
    {
        requiertConnexion();

        $userId = $_SESSION['user_id'];
        $pageTitle = "Transactions";

        // Filters from GET
        $filters = [
            'search'    => trim($_GET['search']    ?? ''),
            'category'  => trim($_GET['category']  ?? ''),
            'type'      => trim($_GET['type']      ?? ''),
            'date_from' => trim($_GET['date_from'] ?? ''),
            'date_to'   => trim($_GET['date_to']   ?? ''),
        ];

        $transactions = $this->model->getByUser($userId, $filters);

        // Pagination
        $perPage     = 8;
        $totalItems  = count($transactions);
        $totalPages  = max(1, (int) ceil($totalItems / $perPage));
        $currentPage = max(1, min((int) ($_GET['p'] ?? 1), $totalPages));
        $offset      = ($currentPage - 1) * $perPage;
        $paginatedTx = array_slice($transactions, $offset, $perPage);

        // Data for dropdowns
        $budgets    = $this->getBudgetsForUser($userId);
        $categories = $this->getCategoriesForUser($userId);

        require_once __DIR__ . '/../../frontend/pages/transactions.php';
    }

    private function getCategoriesForUser(int $userId): array
{
    $stmt = getDB()->prepare('
        SELECT id_categorie, nom_categorie, couleur, icone, est_systeme
        FROM categories
        WHERE est_systeme = 1
           OR id_createur = ?
        ORDER BY est_systeme DESC, nom_categorie ASC
    ');
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

private function getBudgetsForUser(int $userId): array
{
    $stmt = getDB()->prepare('
        SELECT b.id_budget, b.budget_name, b.budget_type
        FROM budgets b
        WHERE
           (
              b.created_by = :userId
              OR EXISTS (
                  SELECT 1 FROM budget_members bm
                  WHERE bm.id_budget = b.id_budget AND bm.id_utilisateur = :userId2
              )
          )
        ORDER BY b.budget_type ASC, b.budget_name ASC
    ');
    $stmt->execute([':userId' => $userId, ':userId2' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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